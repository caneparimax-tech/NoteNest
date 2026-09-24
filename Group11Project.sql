DROP TABLE IF EXISTS Notes, Enrollment, Courses, Sections, Admins, Instructors, Students, UserCredentials;

CREATE TABLE UserCredentials (
    UserID int AUTO_INCREMENT PRIMARY KEY,
    Email varchar(100) UNIQUE NOT NULL,
    Password varchar(255) NOT NULL, 
    Role ENUM('Student', 'Instructor', 'Admin') NOT NULL
);

CREATE TABLE Students (
    StudentID int PRIMARY KEY,
    UserID int UNIQUE,
    FName varchar(50),
    LName varchar(50),
    FOREIGN KEY (UserID) REFERENCES UserCredentials(UserID)
);

CREATE TABLE Instructors (
    InstructorID int PRIMARY KEY,
    UserID int UNIQUE,
    FName varchar(50) NOT NULL,
    LName varchar(50) NOT NULL,
    FOREIGN KEY (UserID) REFERENCES UserCredentials(UserID)
);

CREATE TABLE Admins (
    AdminID int PRIMARY KEY,
    UserID int UNIQUE,
    FName varchar(50) NOT NULL,
    LName varchar(50) NOT NULL,
    FOREIGN KEY (UserID) REFERENCES UserCredentials(UserID)
);

CREATE TABLE Courses (
    CourseID int AUTO_INCREMENT PRIMARY KEY,
    CourseCode varchar(25) NOT NULL,
    CourseName varchar(255) NOT NULL
);

CREATE TABLE Sections (
    SectionID int AUTO_INCREMENT PRIMARY KEY, 
    CourseID int,
    InstructorID int,
    FOREIGN KEY (CourseID) REFERENCES Courses(CourseID),
    FOREIGN KEY (InstructorID) REFERENCES Instructors(InstructorID)
);

CREATE TABLE Notes (
    NoteID int AUTO_INCREMENT PRIMARY KEY,
    NoteTitle varchar(50) NOT NULL, 
    NoteDesc varchar(255) NOT NULL,
    FilePath varchar(255),
    UploadDate datetime DEFAULT CURRENT_TIMESTAMP, 
    Verified boolean,
    SectionID int NOT NULL,
    StudentID int,
    FOREIGN KEY (SectionID) REFERENCES Sections(SectionID),
    FOREIGN KEY (StudentID) REFERENCES Students(StudentID)
);

CREATE TABLE Enrollment (
    SectionID int,
    StudentID int,
    PRIMARY KEY (SectionID, StudentID),
    FOREIGN KEY (SectionID) REFERENCES Sections(SectionID),
    FOREIGN KEY (StudentID) REFERENCES Students(StudentID)
);
