-- ---------------------------------------------------
-- Database: pulsefit_gym
-- Table: contact_enquiries
-- Individual contribution: Trishna
-- Feature: Contact form backend
-- ---------------------------------------------------

CREATE DATABASE IF NOT EXISTS pulsefit_gym;
USE pulsefit_gym;

CREATE TABLE IF NOT EXISTS contact_enquiries (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL,
    phone         VARCHAR(20)   NULL,
    enquiry_type  ENUM('membership', 'personal-training', 'tour', 'feedback') NOT NULL,
    message       TEXT          NOT NULL,
    consent_given TINYINT(1)    NOT NULL DEFAULT 0,
    submitted_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
