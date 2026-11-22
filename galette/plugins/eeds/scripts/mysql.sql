--
-- This file is part of Galette (https://galette.eu).
-- SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
-- SPDX-License-Identifier: GPL-3.0-or-later
--
-- EEDS R1 core membership and structure baseline.
CREATE TABLE IF NOT EXISTS galette_eeds_structures (
  id_structure int unsigned NOT NULL auto_increment,
  parent_structure int unsigned DEFAULT NULL,
  structure_type varchar(50) NOT NULL,
  structure_code varchar(100) NOT NULL,
  structure_name varchar(255) NOT NULL,
  recognition_status varchar(50) NOT NULL DEFAULT 'pending',
  effective_start date NOT NULL,
  effective_end date DEFAULT NULL,
  created_at datetime NOT NULL,
  created_by int unsigned DEFAULT NULL,
  PRIMARY KEY (id_structure),
  UNIQUE KEY galette_eeds_structures_code (structure_code),
  KEY galette_eeds_structures_parent (parent_structure),
  FOREIGN KEY (parent_structure) REFERENCES galette_eeds_structures (id_structure) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (created_by) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS galette_eeds_member_scopes (
  id_scope int unsigned NOT NULL auto_increment,
  id_adh int unsigned NOT NULL,
  id_structure int unsigned NOT NULL,
  scope_permission varchar(100) NOT NULL,
  effective_start date NOT NULL,
  effective_end date DEFAULT NULL,
  created_at datetime NOT NULL,
  created_by int unsigned DEFAULT NULL,
  PRIMARY KEY (id_scope),
  KEY galette_eeds_member_scopes_member (id_adh),
  KEY galette_eeds_member_scopes_structure (id_structure),
  FOREIGN KEY (id_adh) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (id_structure) REFERENCES galette_eeds_structures (id_structure) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (created_by) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS galette_eeds_member_affiliations (
  id_affiliation int unsigned NOT NULL auto_increment,
  id_adh int unsigned NOT NULL,
  id_structure int unsigned NOT NULL,
  branch varchar(100) DEFAULT NULL,
  unit varchar(100) DEFAULT NULL,
  subgroup varchar(100) DEFAULT NULL,
  affiliation_status varchar(50) NOT NULL DEFAULT 'active',
  effective_start date NOT NULL,
  effective_end date DEFAULT NULL,
  reason text,
  evidence_reference varchar(255) DEFAULT NULL,
  created_at datetime NOT NULL,
  created_by int unsigned DEFAULT NULL,
  PRIMARY KEY (id_affiliation),
  KEY galette_eeds_affiliations_member (id_adh),
  KEY galette_eeds_affiliations_structure (id_structure),
  FOREIGN KEY (id_adh) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (id_structure) REFERENCES galette_eeds_structures (id_structure) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (created_by) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS galette_eeds_member_lifecycle (
  id_adh int unsigned NOT NULL,
  membership_status varchar(50) NOT NULL,
  effective_date date NOT NULL,
  updated_at datetime NOT NULL,
  updated_by int unsigned DEFAULT NULL,
  PRIMARY KEY (id_adh),
  FOREIGN KEY (id_adh) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (updated_by) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS galette_eeds_status_history (
  id_status_event int unsigned NOT NULL auto_increment,
  id_adh int unsigned NOT NULL,
  previous_status varchar(50) DEFAULT NULL,
  new_status varchar(50) NOT NULL,
  effective_date date NOT NULL,
  reason text,
  evidence_reference varchar(255) DEFAULT NULL,
  actor_id int unsigned DEFAULT NULL,
  created_at datetime NOT NULL,
  PRIMARY KEY (id_status_event),
  KEY galette_eeds_status_history_member (id_adh),
  FOREIGN KEY (id_adh) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (actor_id) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS galette_eeds_renewals (
  id_renewal int unsigned NOT NULL auto_increment,
  id_adh int unsigned NOT NULL,
  source_period varchar(20) NOT NULL,
  target_period varchar(20) NOT NULL,
  request_date date DEFAULT NULL,
  validation_date date DEFAULT NULL,
  validator_id int unsigned DEFAULT NULL,
  fee_status_reference varchar(100) DEFAULT NULL,
  resulting_status varchar(50) NOT NULL,
  created_at datetime NOT NULL,
  PRIMARY KEY (id_renewal),
  KEY galette_eeds_renewals_member (id_adh),
  FOREIGN KEY (id_adh) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (validator_id) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS galette_eeds_member_numbers (
  id_adh int unsigned NOT NULL,
  national_number varchar(255) NOT NULL,
  issued_at date DEFAULT NULL,
  withdrawn_at date DEFAULT NULL,
  PRIMARY KEY (id_adh),
  UNIQUE KEY galette_eeds_member_numbers_number (national_number),
  FOREIGN KEY (id_adh) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS galette_eeds_duplicate_candidates (
  id_candidate int unsigned NOT NULL auto_increment,
  left_member_id int unsigned NOT NULL,
  right_member_id int unsigned NOT NULL,
  match_fields text NOT NULL,
  confidence varchar(20) NOT NULL,
  candidate_status varchar(20) NOT NULL DEFAULT 'pending',
  reviewer_id int unsigned DEFAULT NULL,
  reviewed_at datetime DEFAULT NULL,
  created_at datetime NOT NULL,
  PRIMARY KEY (id_candidate),
  FOREIGN KEY (left_member_id) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (right_member_id) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (reviewer_id) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS galette_eeds_export_audit (
  id_export int unsigned NOT NULL auto_increment,
  actor_id int unsigned NOT NULL,
  export_fields text NOT NULL,
  structure_ids text,
  row_count int unsigned NOT NULL,
  created_at datetime NOT NULL,
  PRIMARY KEY (id_export),
  FOREIGN KEY (actor_id) REFERENCES galette_adherents (id_adh) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
