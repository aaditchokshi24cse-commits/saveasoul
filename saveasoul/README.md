# SaveASoul – Blood Donation & Donor Management System (v2 — with validation)

## What's new in this version

1. **Phone number validation** — exactly 10 digits, numbers only, enforced both in the browser (pattern + JS auto-strip of non-digits) and on the server (`is_valid_phone()` in `includes/shared_data.php`). Applies to registration.
2. **Email format validation** — proper `name@domain.com` format checked both in the browser (`type="email"`) and on the server (`is_valid_email_format()` using PHP's `FILTER_VALIDATE_EMAIL`). Applies to registration and login.
3. **City selection with suggestions** — a searchable dropdown (HTML `<datalist>`) pre-loaded with 100+ major Indian cities (Gujarat cities prioritized, plus major cities nationwide). You can still type a city not in the list — the list just speeds up typing. Used on Register, Search Donors, Request Blood, and Admin → Add Camp.
4. **Hospital name suggestions** — same datalist approach, pre-loaded with well-known hospitals in Ahmedabad and other major cities. Free text is still allowed for hospitals not listed. Used on Request Blood.
5. **Automatic inventory updates:**
   - When admin clicks **"Log Donation"** for a donor, the donor's blood group inventory automatically **increases by 1 unit** (in addition to logging the donation and updating their next-eligible date).
   - When admin clicks **"Mark Fulfilled"** on a blood request, the system automatically **deducts the requested number of units** from that blood group's inventory. If there isn't enough stock, it blocks the action and tells the admin to top up inventory first.
6. **Stronger required-field validation throughout:**
   - All forms use HTML5 `required` plus server-side checks (never trust the browser alone).
   - Donor age must be 18–65, weight must be at least 45kg (medical donation eligibility).
   - Password must be at least 6 characters.
   - Blood request units must be between 1 and 20.
   - Duplicate email registration is blocked with a clear error message.

## Setup
Same as before — see the original setup steps:
1. Import `sql/saveasoul.sql` into MySQL (via phpMyAdmin)
2. Copy project into XAMPP's `htdocs` folder
3. Update `config.php` with your database credentials
4. Visit `generate_admin_password.php` once to get a working admin password hash, run the UPDATE SQL it shows, then delete that file
5. Login as `admin@saveasoul.com` / `admin123` (or whatever password you set)

## Files changed in this update
- `includes/shared_data.php` — NEW: city list, hospital list, validation helper functions
- `register.php` — full validation + city datalist
- `login.php` — email validation
- `search_donors.php` — city datalist
- `request_blood.php` — hospital + city datalists, units validation
- `admin/donors.php` — log donation now increases inventory
- `admin/requests.php` — fulfill request now decreases inventory (with stock check)
- `admin/camps.php` — city datalist, required field checks
- `admin/inventory.php` — minor safety (no negative values)
- `css/style.css` — added `.field-hint` style for validation hint text
