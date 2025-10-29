CREATE TABLE resumes (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(50),
    email VARCHAR(100),
    linkedin VARCHAR(150),
    website VARCHAR(150),
    objective TEXT,
    education TEXT,
    skills TEXT,
    projects TEXT,
    work TEXT,
    leadership TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
