CREATE UNIQUE INDEX IF NOT EXISTS galette_eeds_structures_national_root
  ON galette_eeds_structures (structure_type)
  WHERE structure_type = 'national';

CREATE TABLE IF NOT EXISTS galette_eeds_structure_groups (
  id_structure integer NOT NULL PRIMARY KEY REFERENCES galette_eeds_structures(id_structure) ON DELETE RESTRICT ON UPDATE CASCADE,
  id_group integer NOT NULL UNIQUE REFERENCES galette_groups(id_group) ON DELETE RESTRICT ON UPDATE CASCADE
);
