<section class="page-hero">
  <div class="container">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= url('membership') ?>">Membership</a></li><li class="breadcrumb-item active">Apply</li></ol></nav>
    <h1 class="mt-2">Apply for Membership</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="form-card" data-aos="fade-up">
          <form method="post" action="<?= url('membership/apply') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="mem-name">Full name</label>
                <input class="form-control" id="mem-name" name="name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-cat">Membership category</label>
                <select class="form-select" id="mem-cat" name="category_id" required>
                  <?php foreach ($categories as $c): ?>
                    <option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?> - <?= format_inr($c['fee']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-photo">Photo</label>
                <input type="file" class="form-control" id="mem-photo" name="photo" accept=".jpg,.jpeg,.png,.webp">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-dob">Date of birth</label>
                <input type="date" class="form-control" id="mem-dob" name="dob">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-gender">Gender</label>
                <select class="form-select" id="mem-gender" name="gender">
                  <option value="">Prefer not to say</option>
                  <option>Male</option><option>Female</option><option>Other</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-blood">Blood group</label>
                <input class="form-control" id="mem-blood" name="blood_group" placeholder="e.g. B+">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-email">Email</label>
                <input type="email" class="form-control" id="mem-email" name="email" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-phone">Phone</label>
                <input class="form-control" id="mem-phone" name="phone" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-occ">Occupation</label>
                <input class="form-control" id="mem-occ" name="occupation">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-aadhar">Aadhar (optional)</label>
                <input class="form-control" id="mem-aadhar" name="aadhar" maxlength="14">
              </div>
              <div class="col-12">
                <label class="form-label" for="mem-address">Address</label>
                <textarea class="form-control" id="mem-address" name="address" rows="2"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-pass">Create a password</label>
                <input type="password" class="form-control" id="mem-pass" name="password" minlength="6" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-pass2">Confirm password</label>
                <input type="password" class="form-control" id="mem-pass2" name="password2" minlength="6" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-cap"><?= e($captcha) ?></label>
                <input class="form-control" id="mem-cap" name="captcha" required>
              </div>
              <div class="col-12 mt-2">
                <button class="btn btn-donate btn-lg" type="submit">Submit application</button>
                <p class="small text-muted mt-2 mb-0">Your application is reviewed by the admin. You'll be able to check status and download your ID card after approval.</p>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
