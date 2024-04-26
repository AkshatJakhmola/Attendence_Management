# Attendence_Management


Users table
CREATE TABLE users (
    teacher_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(128) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(255) NOT NULL,
    course_code VARCHAR(50) NOT NULL,
    teacher_id INT NOT NULL,
    FOREIGN KEY (teacher_id) REFERENCES users(user_id)
);

Students Details table
CREATE TABLE stu_dtls (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(255) NOT NULL,
    roll_number VARCHAR(50) NOT NULL,
    course_code VARCHAR(50) NOT NULL,
    FOREIGN KEY (course_code) REFERENCES courses(course_code)
);

Attendence Table
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    date DATE,
    status VARCHAR(255),
    CONSTRAINT fk_student_id FOREIGN KEY (student_id) REFERENCES stu_dtls(student_id),
    CONSTRAINT fk_course_id FOREIGN KEY (course_id) REFERENCES courses(course_id)
);
