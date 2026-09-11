-- ============================================================
-- PulseFit Gym — Seed data
-- Run after schema.sql. User accounts are seeded separately by
-- sql/seed_users.php since passwords must be hashed by PHP.
-- ============================================================

USE pulsefit_gym;

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
