DROP   DATABASE IF EXISTS TitanCourtTracker;
CREATE DATABASE           TitanCourtTracker;

--Role based access DB users
DROP   USER IF EXISTS 'Observer'@'localhost';
CREATE USER           'Observer'@'localhost' IDENTIFIED BY 'TCT_Observer!';

DROP   USER IF EXISTS 'Users'@'localhost';
CREATE USER           'Users'@'localhost' IDENTIFIED BY 'TCT_Users!';

DROP   USER IF EXISTS 'Executive Manager'@'localhost';
CREATE USER           'Executive Manager'@'localhost' IDENTIFIED BY 'TCT_Manager!';

--Database admin users
DROP USER IF EXISTS 'admin'@'localhost';
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'TCT_Admin!';

GRANT ALL PRIVILEGES ON TitanCourtTracker.* TO 'admin'@'localhost';

USE TitanCourtTracker;

--============
--Roles Table
--============
CREATE TABLE Roles(
    ID        TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(30) NOT NULL UNIQUE COMMENT 'Must match Database Users'
);

GRANT SELECT, INSERT, UPDATE, DELETE ON Roles to 'Executive Manager'@'localhost';

-- NOTE: These values MUST match the Database Users created above
INSERT INTO Roles VALUES
    (1, 'Observer'),
    (2, 'Users'),
    (3, 'Executive Manager');

--============================
--UserLogin Table (Sensitive)
--============================
CREATE TABLE UserLogin(
    ID         INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    role       TINYINT UNSIGNED NOT NULL DEFAULT 1,
    name_first VARCHAR(100),
    name_last  VARCHAR(100) NOT NULL,
    email      VARCHAR(250),
    username   VARCHAR(50) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    last_login DATETIME DEFAULT NULL,
    ts         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (role) REFERENCES Roles(ID) ON DELETE CASCADE,

    CONSTRAINT check_user CHECK (LENGTH(username) >= 3),
    UNIQUE INDEX idx_Username (username)
);

GRANT SELECT, INSERT, UPDATE, DELETE ON UserLogin TO 'Executive Manager'@'localhost';

INSERT INTO UserLogin 
(name_first, name_last, email, username, password, role) VALUES
('Alice', 'Observer', 'alice@test.com', 'alice', '$2y$10$ZZB5.B0QTBRp9vvOMDG32OQET5sAC8bXwDVMOp8Ov0MW9oTSiRzYO', 1),
('Bob',   'Users',    'bob@test.com',   'bob',   '$2y$10$mNgH8HugHuqDKPbWrAq1Net4xohT2BKZ3UxkPn8rZDQH9lqkxj9X6', 2),
('Carol', 'Manager',  'carol@test.com', 'carol', '$2y$10$1bBdN3wIGVfX8hSpujdIAuGjsfhjedcmkLDuJMT4VKYqQ7lf.az82', 3);

--===================
--LoginHistory Table
--===================
CREATE TABLE LoginHistory(
    ID         INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id    INTEGER UNSIGNED NOT NULL,
    login_time DATETIME NOT NULL DEFAULT NOW(),

    FOREIGN KEY (user_id) REFERENCES UserLogin(ID) ON DELETE CASCADE,

    INDEX idx_UserID (user_id),
    INDEX idx_LoginTime (login_time)
);

GRANT SELECT, INSERT ON LoginHistory TO 'Observer'@'localhost';
GRANT SELECT, INSERT ON LoginHistory TO 'Users'@'localhost';
GRANT SELECT, INSERT ON LoginHistory TO 'Executive Manager'@'localhost';

INSERT INTO LoginHistory 
(user_id, login_time) VALUES
(1, '2025-04-10 08:15:00'),
(1, '2025-04-12 09:30:00'),
(2, '2025-04-11 14:00:00'),
(2, '2025-04-13 10:45:00'),
(2, '2025-04-14 16:20:00'),
(3, '2025-04-10 07:00:00'),
(3, '2025-04-11 08:30:00'),
(3, '2025-04-12 09:15:00'),
(3, '2025-04-13 11:00:00'),
(3, '2025-04-14 13:30:00');

--=================
--LeagueTeam Table
--=================
CREATE TABLE LeagueTeam(
    ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    team_name  VARCHAR(100) NOT NULL,
    head_coach VARCHAR(100),
    conference VARCHAR(100) DEFAULT 'Big West',
    city       VARCHAR(100),
    state      VARCHAR(100),
    is_csuf    BOOL DEFAULT FALSE,

    UNIQUE INDEX idx_TeamName (team_name)
);

GRANT SELECT ON LeagueTeam TO 'Observer'@'localhost';
GRANT INSERT, SELECT, UPDATE, DELETE ON LeagueTeam TO 'Users'@'localhost';
GRANT INSERT, SELECT, UPDATE, DELETE ON LeagueTeam TO 'Executive Manager'@'localhost';

INSERT INTO LeagueTeam
(ID, team_name, head_coach, conference, city, state, is_csuf) VALUES
(1, 'CSUF Titans',      'Deadpool',   'Big West', 'Fullerton',     'CA', TRUE),
(2, 'UCI Anteaters',    'Batman',     'Big West', 'Irvine',        'CA', FALSE),
(3, 'CSULB Wildcats',   'Daredevil',  'Big West', 'Long Beach',    'CA', FALSE),
(4, 'UCR GreenHornets', 'Hulk',       'Big West', 'Riverside',     'CA', FALSE),
(5, 'UCSD Bees',        'Robin',      'Big West', 'San Diego',     'CA', FALSE),
(6, 'UCLA Bruins',      'Invincible', 'Pac-12',   'Los Angeles',   'CA', FALSE),
(7, 'CSUSB Wolfs',      'Joker',      'Big West', 'San Bernardino','CA', FALSE),
(8, 'CSULA Eagles',     'Vin Diesel', 'CCAA',     'Los Angeles',   'CA', FALSE);

--=============
--Player Table
--=============
CREATE TABLE Player(
    ID            INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name_first    VARCHAR(100),
    name_last     VARCHAR(100)         NOT NULL,
    jersey_number TINYINT(2) UNSIGNED  NOT NULL,
    position      VARCHAR(50),
    class         ENUM('Freshman','Sophomore','Junior','Senior'),
 
    CONSTRAINT check_jersey CHECK (jersey_number BETWEEN 0 AND 99),
 
    UNIQUE INDEX idx_Jersey (jersey_number),
    UNIQUE INDEX idx_FullName (name_first, name_last),
    INDEX idx_NameLast (name_last)
);

GRANT SELECT ON Player TO 'Observer'@'localhost';
GRANT INSERT, SELECT, UPDATE, DELETE ON Player TO 'Users'@'localhost';
GRANT INSERT, SELECT, UPDATE, DELETE ON Player TO 'Executive Manager'@'localhost';

INSERT INTO Player
(ID, name_first, name_last, jersey_number, position, class) VALUES
(100, 'Donald',               'Duck',   3,  'Point Guard', 'Junior'),
(101, 'Daisy',                'Duck',   10, 'Shooting Guard', 'Senior'),
(102, 'Mickey',               'Mouse',  21, 'Small Forward', 'Sophomore'),
(103, 'Pluto',                'Dog',    34, 'Power Forward', 'Junior'),
(104, 'Scrooge',              'McDuck', 50, 'Center', 'Freshman'),
(105, 'Huebert (Huey)',       'Duck',   5,  'Point Guard', 'Senior'),
(106, 'Deauteronomy (Dewey)', 'Duck',   14, 'Shooting Guard', 'Junior'),
(107, 'Louie',                'Duck',   23, 'Small Forward', 'Sophomore'),
(108, 'Phooey',               'Duck',   42, 'Power Forward', 'Senior'),
(109, 'Della',                'Duck',   55, 'Center', 'Junior');

--===========================
--PlayerPI Table (Sensitive)
--===========================
CREATE TABLE PlayerPI(
    ID        INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    player_id INTEGER UNSIGNED NOT NULL,
    dob       DATE,
    phone     VARCHAR(15),
    email     VARCHAR(100),
    street    VARCHAR(100),
    city      VARCHAR(100),
    state     VARCHAR(100),
    country   VARCHAR(100),
    zip_code  CHAR(10),

    FOREIGN KEY (player_id) REFERENCES Player(ID) ON DELETE CASCADE,

    CONSTRAINT check_zip CHECK (zip_code REGEXP '^(?!0{5})(?!9{5})\\d{5}(-(?!0{4})(?!9{4})\\d{4})?$'),

    UNIQUE INDEX idx_PlayerID (player_id)
);

GRANT INSERT, SELECT, UPDATE, DELETE ON PlayerPI TO 'Executive Manager'@'localhost';

INSERT INTO PlayerPI
(player_id, dob, phone, email, street, city, state, country, zip_code) VALUES
(100, '2001-03-15', '714-555-0101', 'DonaldD@test.com',  '1313 S. Harbor Blvd.', 'Anaheim',          'CA', 'USA', '92808-3232'),
(101, '2000-07-22', '714-555-0102', 'DaisyD@test.com',   '1180 Seven Seas Dr.',  'Lake Buena Vista', 'FL', 'USA', '32830'),
(102, '2002-11-08', '714-555-0103', 'MickeyM@test.com',  '1313 S. Harbor Blvd.', 'Anaheim',          'CA', 'USA', '92808-3232'),
(103, '2001-07-23', '714-555-9287', 'PlutoD@test.com',   '1313 S. Harbor Blvd.', 'Anaheim',          'CA', 'USA', '92808-3232'),
(104, '2000-01-01', '714-555-4356', 'ScroogeD@test.com', '1180 Seven Seas Dr.',  'Lake Buena Vista', 'FL', 'USA', '32830');

--==================================
--PlayerAcademics Table (Sensitive)
--==================================
CREATE TABLE PlayerAcademics(
    ID                 INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    player_id          INTEGER UNSIGNED NOT NULL,
    gpa                DECIMAL(3,2),
    major              VARCHAR(100),
    eligibility_status ENUM('Eligible','Probation','Ineligible'),
    scholarship_amount DECIMAL(10,2),
    academic_advisor   VARCHAR(100),

    FOREIGN KEY (player_id) REFERENCES Player(ID) ON DELETE CASCADE,

    CONSTRAINT check_gpa CHECK (gpa BETWEEN 0.00 AND 4.00),

    UNIQUE INDEX idx_PlayerID (player_id)
);

GRANT INSERT, SELECT, UPDATE, DELETE ON PlayerAcademics TO 'Executive Manager'@'localhost';

INSERT INTO PlayerAcademics 
(player_id, gpa, major, eligibility_status, scholarship_amount, academic_advisor) VALUES
(100, 2.75, 'Business Administration', 'Eligible',  0.00,     'Captain America'),
(101, 3.83, 'Communications',          'Eligible',  6500.00,  'Black Widow'),
(102, 3.50, 'Art',                     'Eligible',  5000.00,  'Captain America'),
(103, 1.95, 'Kinesiology',             'Probation', 0.00,     'Black Panther'),
(104, 3.00, 'Business Administration', 'Eligible',  14000.00, 'Black Panther');

--========================
--Staff Table (Sensitive)
--========================
CREATE TABLE Staff(
    ID         INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name_first VARCHAR(100),
    name_last  VARCHAR(100) NOT NULL,
    title      VARCHAR(100),
    email      VARCHAR(100),
    phone      VARCHAR(15),
    salary     DECIMAL(10,2),
 
    UNIQUE INDEX idx_FullName (name_last, name_first),
    INDEX idx_NameLast (name_last)
);

GRANT INSERT, SELECT, UPDATE, DELETE ON Staff TO 'Executive Manager'@'localhost';
GRANT SELECT ON Staff TO 'Observer'@'localhost';
GRANT SELECT ON Staff TO 'Users'@'localhost';

INSERT INTO Staff
(ID, name_first, name_last, title, email, phone, salary) VALUES
(1, 'Fa', 'Mulan', 'Director of Basketball Operations', 'FaM@test.com', NULL, 94000.00),
(2, 'Kim', 'Possible', 'Sports Information Director', 'KimP@test.com', '714-555-9876', 75000.00),
(3, 'Jim', 'Hawkins', 'Assistant Coach', 'JimH@test.com', '714-555-3280', 104000.00),
(4, 'James', 'Hook', 'Assistant Coach', 'JamesH@test.com', '714-555-2222', 110000.00),
(5, 'John', 'Smith', 'Head Coach', 'JohnS@test.com', '714-555-1700', 152000.00);

--============
--Coach Table
--============
CREATE TABLE Coach(
    ID INTEGER    UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    staff_id      INTEGER  UNSIGNED NOT NULL,
    is_head_coach BOOL DEFAULT FALSE,
 
    FOREIGN KEY (staff_id) REFERENCES Staff(ID) ON DELETE CASCADE,
 
    UNIQUE INDEX idx_StaffID (staff_id)
);

GRANT SELECT ON Coach TO 'Observer'@'localhost';
GRANT INSERT, SELECT, UPDATE, DELETE ON Coach TO 'Users'@'localhost';
GRANT INSERT, SELECT, UPDATE, DELETE ON Coach TO 'Executive Manager'@'localhost';

INSERT INTO Coach
(ID, staff_id, is_head_coach) VALUES
(1, 3, FALSE),
(2, 4, FALSE),
(3, 5, TRUE);

--============
--Game Table
--============
CREATE TABLE Game(
    ID           INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    opponent_id  INTEGER UNSIGNED NOT NULL,
    coach        INTEGER UNSIGNED NOT NULL,
    season_year  VARCHAR (9) NOT NULL,
    game_date    DATE NOT NULL,
    game_time    TIME,
    location     VARCHAR(100),
    home_or_away ENUM('Home','Away') NOT NULL,
    game_type    ENUM('Regular Season','Conference','Tournament'),
    outcome      ENUM('Win','Loss','TBD') DEFAULT 'TBD',
    csuf_score   TINYINT(3) UNSIGNED DEFAULT 0,
    opp_score    TINYINT(3) UNSIGNED DEFAULT 0,
 
    FOREIGN KEY (opponent_id) REFERENCES LeagueTeam(ID) ON DELETE CASCADE,
    FOREIGN KEY (coach)       REFERENCES Coach(ID)      ON DELETE CASCADE,
 
    CONSTRAINT check_scores      CHECK (csuf_score >= 0 AND opp_score >= 0),
    CONSTRAINT check_season_year CHECK (
        season_year REGEXP '^[0-9]{4}-[0-9]{4}$'
        AND CAST(LEFT(season_year, 4) AS UNSIGNED) > 0
        AND CAST(RIGHT(season_year, 4) AS UNSIGNED) > 0
        AND CAST(RIGHT(season_year, 4) AS UNSIGNED) = CAST(LEFT(season_year, 4) AS UNSIGNED) + 1
    ),
 
    INDEX idx_SeasonYear (season_year),
    INDEX idx_OpponentID (opponent_id),
    INDEX idx_Coach (coach)
);

GRANT SELECT ON Game TO 'Observer'@'localhost';
GRANT INSERT, SELECT, UPDATE, DELETE ON Game TO 'Users'@'localhost';
GRANT INSERT, SELECT, UPDATE, DELETE ON Game TO 'Executive Manager'@'localhost';

INSERT INTO Game 
(ID, opponent_id, coach, season_year, game_date, game_time, location, home_or_away, game_type, outcome, csuf_score, opp_score) VALUES
(1, 2, 3, '2025-2026', '2026-01-10', '19:00:00', 'Titan Gym, Fullerton CA', 'Home', 'Regular Season', 'Win', 78, 65),
(2, 3, 3, '2025-2026', '2026-01-17', '19:00:00', 'Walter Pyramid, Long Beach', 'Away', 'Regular Season', 'Loss', 61, 70),
(3, 4, 2, '2025-2026', '2026-01-24', '19:00:00', 'Titan Gym, Fullerton CA', 'Home', 'Regular Season', 'Win', 85, 72),
(4, 5, 3, '2025-2026', '2026-01-31', '19:00:00', 'RIMAC Arena, San Diego', 'Away', 'Conference', 'Win', 90, 88),
(5, 6, 3, '2025-2026', '2026-05-07', '19:00:00', 'Titan Gym, Fullerton CA', 'Home', 'Regular Season', 'TBD', 0, 0),
(6, 5, 2, '2024-2025', '2025-01-14', '19:00:00', 'RIMAC Arena, San Diego', 'Away', 'Conference', 'Loss', 88, 90);

--=====================
--GameStatistics Table
--=====================
CREATE TABLE GameStatistics
(
    ID                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    game_id             INTEGER UNSIGNED NOT NULL,
    player_id           INTEGER UNSIGNED NOT NULL,
    playing_time_min    TINYINT(2) UNSIGNED DEFAULT 0,
    playing_time_sec    TINYINT(2) UNSIGNED DEFAULT 0,
    points              TINYINT(3) UNSIGNED DEFAULT 0,
    assists             TINYINT(3) UNSIGNED DEFAULT 0,
    rebounds            TINYINT(3) UNSIGNED DEFAULT 0,
    free_throw          TINYINT(3) UNSIGNED DEFAULT 0,
    free_throw_attempts TINYINT(3) UNSIGNED DEFAULT 0,
    steals              TINYINT(3) UNSIGNED DEFAULT 0,
    blocks              TINYINT(3) UNSIGNED DEFAULT 0,
    turnovers           TINYINT(3) UNSIGNED DEFAULT 0,
    fouls               TINYINT(1) UNSIGNED DEFAULT 0,
 
    FOREIGN KEY (game_id)   REFERENCES Game(ID)   ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES Player(ID) ON DELETE CASCADE,
 
    CONSTRAINT check_time_range CHECK (
        playing_time_min BETWEEN 0 AND 40 AND
        playing_time_sec BETWEEN 0 AND 59
    ),
 
    UNIQUE INDEX idx_PlayerGame (player_id, game_id)
);

GRANT SELECT ON GameStatistics TO 'Observer'@'localhost';
GRANT INSERT, SELECT, UPDATE, DELETE ON GameStatistics TO 'Users'@'localhost';
GRANT INSERT, SELECT, UPDATE, DELETE ON GameStatistics TO 'Executive Manager'@'localhost';

INSERT INTO GameStatistics 
(game_id, player_id, playing_time_min, playing_time_sec, points, assists, rebounds, free_throw, free_throw_attempts, steals, blocks, turnovers, fouls) VALUES

(1, 101, 35, 20, 22, 5, 3, 4, 5, 2, 0, 3, 2),
(1, 102, 28, 10, 18, 3, 2, 2, 3, 1, 1, 2, 3),
(1, 103, 32,  0, 15, 2, 8, 3, 4, 0, 2, 1, 2),
(1, 104, 25, 45, 10, 1, 7, 2, 2, 1, 3, 2, 3),
(1, 105, 20,  0,  8, 0, 5, 1, 2, 0, 1, 1, 2),

(2, 101, 38,  0, 19, 6, 2, 3, 4, 3, 0, 4, 3),
(2, 102, 30, 15, 14, 2, 3, 2, 3, 1, 0, 3, 2),
(2, 103, 28, 30, 12, 1, 6, 1, 2, 0, 1, 2, 3),
(2, 104, 22,  0,  8, 2, 5, 2, 4, 0, 2, 1, 2),
(2, 105, 18, 45,  5, 0, 4, 0, 1, 0, 1, 2, 3),

(3, 101, 36,  0, 25, 7, 4, 5, 6, 4, 0, 2, 1),
(3, 102, 32, 20, 20, 4, 3, 3, 4, 2, 1, 1, 2),
(3, 103, 30,  0, 18, 2, 9, 4, 5, 1, 3, 0, 1);

FLUSH PRIVILEGES;
