-- ============================================================
-- PulseFit Gym — Database Schema
-- ICT726 Assignment 4 (Dynamic Website)
-- ============================================================

CREATE DATABASE IF NOT EXISTS pulsefit_gym
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE pulsefit_gym;

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
