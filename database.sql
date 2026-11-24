CREATE TABLE karyawan (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  jabatan VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  gaji NUMERIC(12,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO karyawan (nama, jabatan, email, gaji) VALUES
('Budi Santoso', 'Manager', 'budi@example.com', 8500000),
('Siti Aminah', 'Staf Admin', 'siti@example.com', 4500000),
('Andi Wijaya', 'Programmer', 'andi@example.com', 7000000);
