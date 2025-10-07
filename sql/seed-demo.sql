-- sql/seed-demo.sql (sample demo data)

INSERT INTO users (email,password_hash,role,name,created_at) VALUES
('seeker@example.com',  '$2y$10$VqH1zYq7i0oTqAq3a6f1cO7n3l8x2FrxW4SOKC1yJb8y0b1kzS2pS', 'seeker','Ada Seeker',   NOW()),
('employer@example.com','$2y$10$VqH1zYq7i0oTqAq3a6f1cO7n3l8x2FrxW4SOKC1yJb8y0b1kzS2pS', 'employer','Acme HR',    NOW());

-- password hash above is for "password123"

INSERT INTO jobs (employer_user_id,title,description,location,category,status,created_at) VALUES
(2,'PHP Developer','Build and maintain procedural PHP services.','Remote','Software','published',NOW()),
(2,'Frontend Engineer','Implement modern UI and UX.','Lagos','Software','published',NOW()),
(2,'WordPress Engineer','Customize plugins and themes.','Remote','Software','published',NOW());
