ALTER TABLE galette_eeds_structures
  ADD COLUMN national_root tinyint GENERATED ALWAYS AS (
    CASE WHEN structure_type = 'national' THEN 1 ELSE NULL END
  ) STORED,
  ADD UNIQUE KEY galette_eeds_structures_national_root (national_root);

CREATE TABLE IF NOT EXISTS galette_eeds_structure_groups (
  id_structure int unsigned NOT NULL,
  id_group int unsigned NOT NULL,
  PRIMARY KEY (id_structure),
  UNIQUE KEY galette_eeds_structure_groups_group (id_group),
  FOREIGN KEY (id_structure) REFERENCES galette_eeds_structures (id_structure) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (id_group) REFERENCES galette_groups (id_group) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
