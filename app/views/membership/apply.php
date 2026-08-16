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
          <form method="post" action="<?= url('membership/apply') ?>" enctype="multipart/form-data" data-location-group>
            <?= csrf_field() ?>
            <h6 class="fw-bold mb-3">Personal details</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label" for="mem-fname">First name</label>
                <input class="form-control" id="mem-fname" name="first_name" required>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="mem-mname">Middle name</label>
                <input class="form-control" id="mem-mname" name="middle_name">
              </div>
              <div class="col-md-4">
                <label class="form-label" for="mem-sname">Surname</label>
                <input class="form-control" id="mem-sname" name="surname" required>
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
                <input type="date" class="form-control" id="mem-dob" name="dob" max="<?= date('Y-m-d') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-gender">Gender</label>
                <select class="form-select" id="mem-gender" name="gender" required>
                  <option value="">Select gender</option>
                  <option>Male</option><option>Female</option><option>Other</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-blood">Blood group</label>
                <input class="form-control" id="mem-blood" name="blood_group" placeholder="e.g. B+">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-occ">Occupation</label>
                <input class="form-control" id="mem-occ" name="occupation">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-email">Email</label>
                <input type="email" class="form-control" id="mem-email" name="email" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="mem-phone">Phone</label>
                <input class="form-control" id="mem-phone" name="phone" required>
              </div>
            </div>

            <h6 class="fw-bold mb-3 mt-4">Address</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label" for="mem-state">State</label>
                <select class="form-select" id="mem-state" name="state_id" data-location="state" required>
                  <option value="">Select State</option>
                  <?php foreach ($states as $s): ?>
                    <option value="<?= (int) $s['id'] ?>"><?= e($s['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="mem-district">District</label>
                <select class="form-select" id="mem-district" name="district_id" data-location="district" disabled required>
                  <option value="">Select State First</option>
                </select>
                <input type="text" class="form-control mt-2 d-none" id="mem-district-other" name="district_other" data-other-for="district" placeholder="Enter your district" disabled>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="mem-taluka">Taluka</label>
                <select class="form-select" id="mem-taluka" name="taluka_id" data-location="taluka" disabled required>
                  <option value="">Select District First</option>
                </select>
                <input type="text" class="form-control mt-2 d-none" id="mem-taluka-other" name="taluka_other" data-other-for="taluka" placeholder="Enter your taluka" disabled>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="mem-pincode">Pincode</label>
                <input class="form-control" id="mem-pincode" name="pincode" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required>
              </div>
              <div class="col-12">
                <label class="form-label" for="mem-address">Address</label>
                <textarea class="form-control" id="mem-address" name="address" rows="2" required></textarea>
              </div>
            </div>

            <h6 class="fw-bold mb-3 mt-4">Identity proof</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label" for="mem-idtype">ID proof type</label>
                <select class="form-select" id="mem-idtype" name="id_proof_type" required>
                  <option value="">Select ID proof type</option>
                  <?php foreach ($idProofTypes as $val => $label): ?>
                    <option value="<?= e($val) ?>"><?= e($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="mem-idnum">Document number</label>
                <input class="form-control" id="mem-idnum" name="id_proof_number" required>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="mem-idfile">Upload ID proof</label>
                <input type="file" class="form-control" id="mem-idfile" name="id_proof_file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
              </div>
            </div>

            <h6 class="fw-bold mb-3 mt-4">Account</h6>
            <div class="row g-3">
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
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="mem-terms" name="terms" value="1" required>
                  <label class="form-check-label small" for="mem-terms">
                    I agree to the <a href="<?= url('page/terms') ?>" target="_blank" rel="noopener noreferrer">Terms &amp; Conditions</a>
                    and <a href="<?= url('page/privacy') ?>" target="_blank" rel="noopener noreferrer">Privacy Policy</a>.
                  </label>
                </div>
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
