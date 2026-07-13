<?php
class DonateController extends Controller
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePledge(null);
        }
        $this->render('donate/index', [
            'pageTitle' => 'Donate Now',
            'campaigns' => Database::all("SELECT * FROM campaigns WHERE is_active=1 ORDER BY id DESC LIMIT 3"),
            'captcha'   => captcha_question(),
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
            'pageTitle' => $campaign['title'],
            'metaDesc'  => $campaign['summary'] ?? '',
            'campaign'  => $campaign,
            'donors'    => Database::all("SELECT donor_name, amount, created_at FROM donations
                                          WHERE campaign_id=? AND status='received' ORDER BY id DESC LIMIT 10", [$campaign['id']]),
            'captcha'   => captcha_question(),
        ]);
    }

    public function sponsor(): void
    {
        $this->render('donate/sponsor', [
            'pageTitle' => 'Sponsorship Programs',
            'sponsors'  => Database::all("SELECT * FROM sponsors ORDER BY sort_order"),
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
        $amount = (float) post('amount');
        if (!captcha_verify()) {
            flash_set('error', 'Captcha answer was wrong. Please try again.');
        } elseif (post('donor_name') === '' || $amount < 1) {
            flash_set('error', 'Please enter your name and a valid amount.');
        } else {
            $receipt = generate_receipt_no();
            Database::insert('donations', [
                'receipt_no'  => $receipt,
                'campaign_id' => $campaignId,
                'donor_name'  => post('donor_name'),
                'email'       => post('email'),
                'phone'       => post('phone'),
                'amount'      => $amount,
                'method'      => in_array(post('method'), ['upi','bank','cash','online']) ? post('method') : 'upi',
                'txn_ref'     => post('txn_ref'),
                'pan'         => post('pan'),
                'message'     => post('message'),
            ]);
            flash_set('success', "Thank you! Your donation pledge is recorded (Ref: $receipt). Please complete the transfer using the UPI/bank details shown. Our team will verify it and email your 80G receipt.");
        }
        redirect($redirectTo);
    }
}
