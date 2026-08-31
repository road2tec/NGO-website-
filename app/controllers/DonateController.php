<?php
class DonateController extends Controller
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campaignId = (int) post('campaign_id') ?: null;
            if ($campaignId && !Database::value("SELECT COUNT(*) FROM campaigns WHERE id=? AND is_active=1", [$campaignId])) {
                $campaignId = null; // tampered/stale campaign id - fall back to the general fund rather than trust it
            }
            $this->handlePledge($campaignId);
        }
        $this->render('donate/index', [
            'pageTitle'     => 'Donate Now',
            'campaigns'     => Database::all("SELECT * FROM campaigns WHERE is_active=1 ORDER BY id DESC"),
            'amountOptions' => Database::all("SELECT * FROM donation_amount_options WHERE is_active=1 ORDER BY sort_order"),
        ]);
    }

    public function campaigns(): void
    {
        $this->render('donate/campaigns', [
            'pageTitle' => 'Crowdfunding Campaigns',
            'active'    => Database::all("SELECT * FROM campaigns WHERE is_active=1 ORDER BY id DESC"),
            'closed'    => Database::all("SELECT * FROM campaigns WHERE is_active=0 ORDER BY id DESC LIMIT 6"),
        ]);
    }

    public function campaign(?string $slug = null): void
    {
        $campaign = $slug ? Database::one("SELECT * FROM campaigns WHERE slug=?", [$slug]) : null;
        if (!$campaign) $this->notFound('Campaign not found.');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePledge((int) $campaign['id'], 'donate/campaign/' . $campaign['slug']);
        }

        $this->render('donate/campaign', [
            'pageTitle'     => $campaign['title'],
            'metaDesc'      => $campaign['summary'] ?? '',
            'campaign'      => $campaign,
            'donors'        => Database::all("SELECT donor_name, amount, created_at FROM donations
                                          WHERE campaign_id=? AND status='received' ORDER BY id DESC LIMIT 10", [$campaign['id']]),
            'amountOptions' => Database::all("SELECT * FROM donation_amount_options WHERE is_active=1 ORDER BY sort_order"),
        ]);
    }

    public function sponsor(): void
    {
        $this->render('donate/sponsor', [
            'pageTitle' => 'Sponsorship Programs',
            'sponsors'  => Database::all("SELECT * FROM sponsors ORDER BY sort_order"),
        ]);
    }

    /** Public QR verification endpoint for donation certificates. */
    public function certificate(?string $code = null): void
    {
        $donation = $code ? Database::one(
            "SELECT d.*, c.title AS campaign_title FROM donations d
             LEFT JOIN campaigns c ON c.id = d.campaign_id WHERE d.cert_code = ?", [$code]
        ) : null;
        $this->render('donate/certificate_verify', [
            'pageTitle' => 'Verify Donation Certificate',
            'donation'  => $donation,
            'code'      => $code,
        ]);
    }

    /**
     * Records a donation pledge as "pending". The admin marks it "received"
     * after verifying the UPI / bank transfer. Replace with a payment
     * gateway callback (Razorpay/PayU) when going live with online payments.
     */
    private function handlePledge(?int $campaignId, string $redirectTo = 'donate'): void
    {
        require_csrf();
        $errors = [];

        $firstName  = post('first_name');
        $middleName = post('middle_name');
        $surname    = post('surname');
        if ($firstName === '')            $errors[] = 'First name is required.';
        if ($surname === '')              $errors[] = 'Surname is required.';
        if (!valid_email(post('email'))) $errors[] = 'A valid email is required.';
        if (!valid_phone(post('phone'))) $errors[] = 'A valid phone number is required.';

        $amountChoice = post('amount_option_id');
        $amount = 0.0;
        if ($amountChoice === 'custom') {
            $amount = (float) post('custom_amount');
            if ($amount < 1) $errors[] = 'Please enter a valid custom donation amount.';
        } elseif ($amountChoice !== '' && ctype_digit($amountChoice)) {
            $preset = Database::one("SELECT amount FROM donation_amount_options WHERE id=? AND is_active=1", [(int) $amountChoice]);
            if ($preset) $amount = (float) $preset['amount'];
            else $errors[] = 'Please select a valid donation amount.';
        } else {
            $errors[] = 'Please select a donation amount.';
        }

        if (!captcha_verify()) $errors[] = 'Captcha answer was wrong. Please try again.';

        if ($errors) {
            flash_set('error', implode(' ', $errors));
        } else {
            $receipt = generate_receipt_no();
            $fullName = trim($firstName . ' ' . ($middleName !== '' ? $middleName . ' ' : '') . $surname);
            Database::insert('donations', [
                'receipt_no'  => $receipt,
                'campaign_id' => $campaignId,
                'donor_name'  => $fullName,
                'first_name'  => $firstName,
                'middle_name' => $middleName ?: null,
                'surname'     => $surname,
                'email'       => post('email'),
                'phone'       => post('phone'),
                'address'     => post('address'),
                'amount'      => $amount,
                'method'      => in_array(post('method'), ['upi','bank','cash','online','cheque'], true) ? post('method') : 'upi',
                'txn_ref'     => post('txn_ref'),
                'cheque_no'       => post('cheque_no') ?: null,
                'donor_bank_name' => post('donor_bank_name') ?: null,
                'pan'         => post('pan'),
                'message'     => post('message'),
            ]);
            send_mail(
                post('email'),
                'Thank you for your pledge - ' . setting('site_name'),
                "Dear $fullName,\n\n"
                . "Thank you for pledging " . format_inr($amount) . " to " . setting('site_name') . ".\n\n"
                . "Reference: $receipt\n\n"
                . "Please complete the transfer using the UPI/bank details shown on the donate page if you haven't already. "
                . "Our team will verify the payment and email your 80G donation receipt once confirmed.\n\n"
                . "With gratitude,\n" . setting('site_name'),
                $fullName
            );
            flash_set('success', "Thank you! Your donation pledge is recorded (Ref: $receipt). Please complete the transfer using the UPI/bank details shown. Our team will verify it and email your 80G receipt.");
        }
        redirect($redirectTo);
    }
}
