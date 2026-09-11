-- ============================================================
-- PulseFit Gym — Combined import for InfinityFree (phpMyAdmin)
-- ICT726 Assignment 4 (Dynamic Website)
--
-- InfinityFree gives you one pre-created, pre-named database and
-- no CLI/SSH access, so this file is schema.sql + seed.sql merged
-- into one, with the `CREATE DATABASE` / `USE` statements removed
-- (phpMyAdmin already has your database selected when you import).
--
-- How to use:
--   1. Log in to phpMyAdmin from your InfinityFree control panel.
--   2. Select your database (e.g. if0_42890673_pulsefit) in the
--      left sidebar.
--   3. Click the "Import" tab.
--   4. Choose this file, set "Character set of the file" to
--      utf8mb4, and click "Go".
--
-- Demo user accounts (admin/member/normal) are NOT included here —
-- passwords must be hashed by PHP, not written as plain SQL. Run
-- sql/seed_users.php once after upload (see docs/README.md).
-- ============================================================

-- ------------------------------------------------------------
-- users: everyone who can sign in. Role drives access control.
--   normal = registered, no active membership yet
--   member = has (or has had) a paid membership plan
--   admin  = full back-office access
-- ------------------------------------------------------------
CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(120)      NOT NULL,
  email         VARCHAR(190)      NOT NULL UNIQUE,
  password_hash VARCHAR(255)      NOT NULL,
  role          ENUM('admin','member','normal') NOT NULL DEFAULT 'normal',
  phone         VARCHAR(30)       NULL,
  status        ENUM('active','suspended') NOT NULL DEFAULT 'active',
  created_at    TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- membership_plans: the tiers shown on the Membership page,
-- fully editable by an admin instead of being hard-coded.
-- ------------------------------------------------------------
CREATE TABLE membership_plans (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name           VARCHAR(60)   NOT NULL,
  slug           VARCHAR(60)   NOT NULL UNIQUE,
  price_cents    INT UNSIGNED  NOT NULL,
  billing_cycle  VARCHAR(20)   NOT NULL DEFAULT 'month',
  description    VARCHAR(255)  NULL,
  is_featured    TINYINT(1)    NOT NULL DEFAULT 0,
  display_order  INT           NOT NULL DEFAULT 0,
  created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- one-to-many: a plan has several feature lines (✓ or ✗ in the UI)
CREATE TABLE plan_features (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  plan_id        INT UNSIGNED  NOT NULL,
  feature_text   VARCHAR(160)  NOT NULL,
  is_included    TINYINT(1)    NOT NULL DEFAULT 1,
  display_order  INT           NOT NULL DEFAULT 0,
  FOREIGN KEY (plan_id) REFERENCES membership_plans(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- user_memberships: which plan a user is subscribed to (history)
-- ------------------------------------------------------------
CREATE TABLE user_memberships (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED  NOT NULL,
  plan_id     INT UNSIGNED  NOT NULL,
  start_date  DATE          NOT NULL,
  end_date    DATE          NULL,
  status      ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active',
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (plan_id) REFERENCES membership_plans(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- trainers: coaching staff, linked to the classes they run
-- ------------------------------------------------------------
CREATE TABLE trainers (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name      VARCHAR(80)   NOT NULL,
  role_title     VARCHAR(80)   NOT NULL,
  bio            TEXT          NULL,
  photo_path     VARCHAR(255)  NULL,
  display_order  INT           NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- classes: the weekly schedule, admin-editable
-- ------------------------------------------------------------
CREATE TABLE classes (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(80)   NOT NULL,
  day_of_week  ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  start_time   TIME          NOT NULL,
  end_time     TIME          NOT NULL,
  trainer_id   INT UNSIGNED  NULL,
  capacity     INT UNSIGNED  NOT NULL DEFAULT 20,
  is_new       TINYINT(1)    NOT NULL DEFAULT 0,
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- bookings: a member reserving a specific date's occurrence
-- of a class. Unique constraint stops double-booking the same slot.
-- ------------------------------------------------------------
CREATE TABLE bookings (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id       INT UNSIGNED  NOT NULL,
  class_id      INT UNSIGNED  NOT NULL,
  booking_date  DATE          NOT NULL,
  status        ENUM('booked','cancelled','attended') NOT NULL DEFAULT 'booked',
  created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE RESTRICT,
  UNIQUE KEY uniq_booking (user_id, class_id, booking_date)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- enquiries: the Contact page form, now stored server-side
-- ------------------------------------------------------------
CREATE TABLE enquiries (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(120)  NOT NULL,
  email       VARCHAR(190)  NOT NULL,
  phone       VARCHAR(30)   NULL,
  subject     VARCHAR(60)   NOT NULL,
  message     TEXT          NOT NULL,
  status      ENUM('new','read','replied') NOT NULL DEFAULT 'new',
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- testimonials: member-submitted reviews, admin-moderated
-- ------------------------------------------------------------
CREATE TABLE testimonials (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id       INT UNSIGNED  NULL,
  display_name  VARCHAR(80)   NOT NULL,
  member_label  VARCHAR(80)   NULL,
  rating        TINYINT UNSIGNED NOT NULL DEFAULT 5,
  quote         TEXT          NOT NULL,
  status        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- ============================================================
-- Seed data (from sql/seed.sql)
-- ============================================================

-- ------------------------------------------------------------
-- Membership plans
-- ------------------------------------------------------------
INSERT INTO membership_plans (name, slug, price_cents, billing_cycle, description, is_featured, display_order) VALUES
('Basic',    'basic',    2900, 'month', 'Great for getting started with flexible open-gym access.', 0, 1),
('Standard', 'standard', 5900, 'month', 'Unlimited classes plus regular coaching support.',          1, 2),
('Premium',  'premium',  9900, 'month', 'Full access plus weekly 1-to-1 personal training.',         0, 3);

INSERT INTO plan_features (plan_id, feature_text, is_included, display_order) VALUES
(1, 'Open-gym access (6am–11pm)', 1, 1),
(1, '2 group classes / week',      1, 2),
(1, 'Locker room & showers',       1, 3),
(1, 'Free fitness assessment',     1, 4),
(1, 'Personal training sessions',  0, 5),
(1, '24/7 keycard access',         0, 6),

(2, 'Full open-gym access',        1, 1),
(2, 'Unlimited group classes',     1, 2),
(2, 'Monthly coach check-in',      1, 3),
(2, '1 guest pass / month',        1, 4),
(2, 'Locker room & showers',       1, 5),
(2, '24/7 keycard access',         0, 6),

(3, '24/7 studio keycard access',  1, 1),
(3, 'Unlimited group classes',     1, 2),
(3, 'Weekly 1-to-1 PT session',    1, 3),
(3, 'Personalised nutrition plan', 1, 4),
(3, '2 guest passes / month',      1, 5),
(3, 'Priority class booking',      1, 6);

-- ------------------------------------------------------------
-- Trainers
-- ------------------------------------------------------------
INSERT INTO trainers (full_name, role_title, bio, photo_path, display_order) VALUES
('Marcus Bell',    'Head Strength Coach',        'Former competitive powerlifter with 12 years of coaching experience, specialising in barbell technique and progressive overload.', 'images/trainer-1.jpg', 1),
('Priya Anand',    'HIIT & Conditioning Coach',  'Designs the studio''s interval classes to build engine and burn fat without wrecking your joints.', 'images/trainer-2.jpg', 2),
('Diego Ferreira', 'Personal Training Lead',     'Specialises in 1-to-1 programming for injury recovery, mobility and long-term strength progression.', 'images/trainer-3.jpg', 3);

-- ------------------------------------------------------------
-- Weekly class schedule
-- ------------------------------------------------------------
INSERT INTO classes (name, day_of_week, start_time, end_time, trainer_id, capacity, is_new) VALUES
('HIIT Blast',      'Monday',    '06:00:00', '06:45:00', 2, 20, 0),
('Strength 101',    'Tuesday',   '06:00:00', '06:45:00', 1, 16, 0),
('HIIT Blast',      'Wednesday', '06:00:00', '06:45:00', 2, 20, 0),
('Strength 101',    'Thursday',  '06:00:00', '06:45:00', 1, 16, 0),
('HIIT Blast',      'Friday',    '06:00:00', '06:45:00', 2, 20, 0),
('Yoga Flow',       'Saturday',  '06:00:00', '06:45:00', 3, 18, 0),

('Spin',            'Monday',    '09:00:00', '09:45:00', 2, 24, 0),
('Bootcamp',        'Tuesday',   '09:00:00', '09:45:00', 1, 20, 0),
('Spin',            'Wednesday', '09:00:00', '09:45:00', 2, 24, 0),
('Bootcamp',        'Thursday',  '09:00:00', '09:45:00', 1, 20, 0),
('Spin',            'Friday',    '09:00:00', '09:45:00', 2, 24, 0),
('Bootcamp',        'Saturday',  '09:00:00', '09:45:00', 1, 20, 0),

('Boxing Fit',      'Monday',    '18:30:00', '19:15:00', 3, 18, 1),
('Yoga Flow',        'Tuesday',   '18:30:00', '19:15:00', 3, 18, 0),
('Boxing Fit',      'Wednesday', '18:30:00', '19:15:00', 3, 18, 1),
('Yoga Flow',        'Thursday',  '18:30:00', '19:15:00', 3, 18, 0),
('Boxing Fit',      'Friday',    '18:30:00', '19:15:00', 3, 18, 1);

-- ------------------------------------------------------------
-- Testimonials (legacy/seeded, not tied to a real user account)
-- ------------------------------------------------------------
INSERT INTO testimonials (user_id, display_name, member_label, rating, quote, status) VALUES
(NULL, 'Sarah M.',  'Member for 2 years',          5, 'I''ve tried three other gyms in this city and none of them come close. The coaches actually watch your form instead of handing you a program and walking away.', 'approved'),
(NULL, 'James O.',  'Premium Member',              5, 'Lost 14kg in eight months without feeling like I was starving myself. The nutrition coaching add-on genuinely changed how I eat.', 'approved'),
(NULL, 'Aisha K.',  'Standard Member',              5, 'The 6am HIIT class is the only reason I''m consistent anymore. Small group, loud music, zero ego — exactly what I needed.', 'approved'),
(NULL, 'Daniel R.', 'Personal Training Client',    5, 'Recovering from a shoulder injury, my coach rebuilt my whole program around what I could actually do. Back to full training in four months.', 'approved'),
(NULL, 'Priya S.',  'Basic Member',                 5, 'Booked a free trial on a whim and signed up before I even left the building. The energy in this place is unmatched.', 'approved'),
(NULL, 'Tom H.',    'Premium Member',              5, '24/7 access means I can train at 5am before work or 10pm after a late shift. Never used to prioritise fitness — now it''s non-negotiable.', 'approved');
