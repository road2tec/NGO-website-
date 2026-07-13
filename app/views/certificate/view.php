<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Certificate of Participation - <?= e($reg['name']) ?></title>
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=Mulish&display=swap" rel="stylesheet">
<style>
  body { background:#eef3f9; padding:2rem 1rem; }
  .cert {
    max-width: 900px; margin: 0 auto; background:#fff; border-radius: 18px;
    box-shadow: var(--shadow-lift); padding: 3.5rem 3rem; position: relative;
    border: 10px solid transparent;
    background-image: linear-gradient(#fff,#fff), var(--band);
    background-origin: border-box; background-clip: padding-box, border-box;
    text-align:center;
  }
  .cert h1 { font-size: 2.1rem; }
  .cert .name { font-family:'Bricolage Grotesque',sans-serif; font-size: 2.4rem; font-weight:800; color: var(--blue-deep); margin: 1rem 0 .3rem; }
  .cert .meta { color: var(--muted); }
  .cert .sign { display:flex; justify-content:space-between; margin-top: 3rem; }
  .actions { max-width:340px; margin:1.4rem auto 0; }
</style>
</head>
<body>
  <div class="cert">
    <div class="eyebrow justify-content-center mb-2">Certificate of Participation</div>
    <h1 class="fw-bold"><?= e(setting('site_name')) ?></h1>
    <p class="text-muted mb-0">This certifies that</p>
    <div class="name"><?= e($reg['name']) ?></div>
    <p class="meta">participated in <strong><?= e($reg['event_title']) ?></strong><br>
    held on <?= format_date($reg['event_date'], 'd F Y') ?> at <?= e($reg['venue']) ?>.</p>

    <div class="sign">
      <div>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=<?= urlencode(BASE_URL . '/certificate/verify/' . $reg['cert_code']) ?>" alt="Verification QR code">
        <div class="small text-muted mt-1">Cert. ID: <?= e($reg['cert_code']) ?></div>
      </div>
      <div class="align-self-end text-end">
        <div class="fw-bold">Program Director</div>
        <div class="small text-muted"><?= e(setting('site_name')) ?></div>
      </div>
    </div>
  </div>

  <div class="actions text-center no-print">
    <button class="btn btn-blue w-100 mt-3" onclick="window.print()">Print / Save as PDF</button>
    <a href="<?= url('') ?>" class="btn btn-outline-nav w-100 mt-2">Back to homepage</a>
  </div>
</body>
</html>
