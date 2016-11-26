-- Run these once then delete to remove old tables
drop table myclient;
drop table admin;
DROP SEQUENCE admin_id;
drop table myclientsession;
drop table take;

-- Starts with fresh tables
drop table enroll cascade constraints;
drop table prereq cascade constraints;
drop table section cascade constraints;
drop table course cascade constraints;
drop table student cascade constraints;
drop table sessions cascade constraints;
drop table users cascade constraints;

-- Sequences
DROP SEQUENCE sequence_number;
CREATE SEQUENCE sequence_number MINVALUE 1 START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;

-- Tables
create table users
(
 clientid varchar2(8) primary key,
 password varchar2(25),
 firstname varchar2(50),
 lastname varchar2(50),
 user_type number(1)
);

create table sessions
(
 sessionid varchar2(32) primary key,
 clientid varchar2(8) not null,
 foreign key (clientid) references users
);

create table student
(
 sid varchar2(20) default 0 primary key,
 age number(8),
 streetnumber varchar2(50),
 streetname varchar2(50),
 city varchar2(50),
 state varchar(50),
 zip number(10),
 status varchar2(20),
 stype varchar2(20),
 clientid varchar2(8) not null,
 gpa number(2,1),
 totalcredits number(3),
 coursescompleted number(3),
 foreign key (clientid) references users
);

create table course
(
 cnumber varchar2(8) primary key,
 cname varchar2(50),
 credits number(2),
 cdesc varchar2(500)
);
 
create table section
(
 seqid varchar(12) primary key,
 max_seat number(8),
 avail_seat number(8),
 time varchar2(30),
 cnumber varchar2(12) not null,
 semester varchar2(15),
 day varchar2(10),
 year varchar2(4),
 foreign key (cnumber) references course
);

create table enroll
(
 sid varchar2(20),
 seqid varchar2(12),
 grade number(8,2),
 constraint take_pk primary key(sid, seqid),
 foreign key (sid) references student,
 foreign key (seqid) references section
);

create table prereq
(
 cnumber varchar2(12),
 prereq varchar2(12),
 constraint prereq_pk primary key(cnumber, prereq),
 foreign key (cnumber) references course,
 foreign key (prereq) references course
);

-- stored procedure to create a student and populate the student id 
CREATE OR REPLACE PROCEDURE generateStudent (
    fname in users.firstname%TYPE,
    lname in users.lastname%TYPE,
    clientid in users.clientid%TYPE,
    password in users.password%TYPE,
    age in student.age%TYPE,
    streetnumber in student.streetnumber%TYPE,
    streetname in student.streetname%TYPE,
    city in student.city%TYPE,
    state in student.state%TYPE,
    zip in student.zip%TYPE,
	status in student.status%TYPE,
    stype in student.stype%TYPE,
    id out student.sid%TYPE) IS 
BEGIN 
    id := SUBSTR(fname, 1, 1) || SUBSTR(lname, 1, 1) || sequence_number.NEXTVAL;
    INSERT into Users(clientid, password, firstname, lastname, user_type) VALUES (clientid, password, fname, lname, 0);
    INSERT into Student(sid, age, streetnumber, streetname, city, state, zip, status, stype, clientid, gpa) VALUES (id, age, streetnumber, streetname, city, state, zip, status, stype, clientid, null); 
END generateStudent;
/


CREATE TABLE temp_student_ID (
   tsid varchar2(20)
);

CREATE OR REPLACE TRIGGER check_for_GPA_update
BEFORE INSERT OR UPDATE OR DELETE ON enroll
FOR EACH ROW
BEGIN
   IF INSERTING THEN
      IF :new.grade IS NOT NULL THEN
         INSERT INTO temp_student_ID values(:new.sid);
         dbms_output.put_line('Insert with grade: ' || :new.grade);
      END IF;
   ELSIF UPDATING THEN
      INSERT INTO temp_student_ID values(:new.sid);
      dbms_output.put_line('Update with grade: ' || :new.grade);
   ELSIF DELETING THEN
      INSERT INTO temp_student_ID values(:old.sid);
   END IF;
END;
/

CREATE OR REPLACE TRIGGER calculate_GPA
AFTER INSERT OR UPDATE OR DELETE ON enroll
DECLARE
   grade class_final_grade.grade%TYPE;
   credit class_final_grade.credits%TYPE;
   total_credits class_final_grade.credits%TYPE;
   total_grade_points class_final_grade.grade%TYPE;
   new_GPA student.gpa%TYPE;
   id temp_student_ID.tsid%TYPE;
   trash_id class_final_grade.sid%TYPE;
   courses_completed class_final_grade.credits%TYPE;

   --For the sid from temp_student_ID 
   CURSOR cursorID IS
      SELECT tsid FROM temp_student_ID;

   --For all grades for a student
   CURSOR cursorGPA IS
      SELECT * FROM class_final_grade
      WHERE class_final_grade.sid = id;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
BEGIN
   OPEN cursorID;
   LOOP
      total_credits:= 0;
      total_grade_points:= 0;
      
      FETCH cursorID into id;
      EXIT WHEN cursorID%NOTFOUND;
      dbms_output.put_line('Cursor id in outer loop: ' || id);
      
      OPEN cursorGPA;
      LOOP
         FETCH cursorGPA INTO trash_id, grade, credit;
         EXIT WHEN cursorGPA%NOTFOUND;
            total_grade_points := total_grade_points + (grade*credit);
            total_credits := total_credits + credit;
            dbms_output.put_line('grade: ' || grade);
            dbms_output.put_line('credit: ' || credit);
      END LOOP;
      CLOSE cursorGPA;
      
      --Calculate GPA and update record
      dbms_output.put_line('total grade points: ' || total_grade_points);
      dbms_output.put_line('total credits: ' || total_credits );
      new_GPA  := (total_grade_points/total_credits);
      courses_completed := (total_credits/3);
      --Update probation status
      IF (new_GPA < 2.0) THEN
         dbms_output.put_line('On probation');
         UPDATE student SET 
            gpa = new_GPA,
			coursescompleted = courses_completed,
			totalcredits = total_credits,
            status = 'YES'
         WHERE id = student.sid;
      ELSE
      dbms_output.put_line('Not on probation');
         UPDATE student SET 
            gpa = new_GPA,
			coursescompleted = courses_completed,
			totalcredits = total_credits,
            status = 'NO'
         WHERE student.sid = id;
      END IF;
   END LOOP;
   CLOSE cursorID;
   DELETE temp_student_ID;
END;
/ 

CREATE OR REPLACE VIEW class_final_grade(sid, grade, credits) AS
   SELECT e.sid, e.grade, c.credits
   FROM enroll e NATURAL JOIN section NATURAL JOIN course c
   WHERE e.grade IS NOT NULL;


-- Test Data for project
-- User Type 1 == Administrator, 0 == Student
insert into users(clientid, password, firstname, lastname, user_type) values('gq030', 'atmdvj', 'Tyrel', 'Tachibana', 1);
insert into users(clientid, password, firstname, lastname, user_type) values('gq037', 'atdmvj', 'Kevin', 'Jaeger', 1);
insert into users(clientid, password, firstname, lastname, user_type) values('gq000', 'professor', 'Gang', 'Qian', 1);

-- generate two students and add to database
SET SERVEROUTPUT ON;
DECLARE
idtest student.sid%TYPE;
BEGIN
generateStudent('Micah', 'McKinnon', 'gq025', 'password', 27, '1', 'Apple St', 'OKC', 'OK', 73120, 'NO', 'Under-Graduate', idtest);
DBMS_OUTPUT.PUT_LINE('Id for Micah is: ' || idtest);
END;
/

DECLARE
idtest student.sid%TYPE;
BEGIN
generateStudent('Michael', 'Keller', 'gq020', 'mypassword', 26, '2', 'Apple St', 'OKC', 'OK', 73003, 'NO', 'Graduate', idtest);
DBMS_OUTPUT.PUT_LINE('Id for Michael is: ' || idtest);
END;
/

DECLARE
idtest student.sid%TYPE;
BEGIN
generateStudent('John', 'Smith', 'gq8001', 'password', 73, '62', 'Apple Way', 'OKC', 'OK', 73003, 'YES', 'Graduate', idtest);
DBMS_OUTPUT.PUT_LINE('Id for John is: ' || idtest);
END;
/

insert into course(cnumber, cname, credits, cdesc) values('CMSC4003', 'Application Database Management', 3, 'Learn about databases');
insert into course(cnumber, cname, credits, cdesc) values('CMSC4063', 'Networks', 3,'Learn about hacking networks');
insert into course(cnumber, cname, credits, cdesc) values('CMSC3413', 'Advanced Visual Programming', 3, 'Learn to make awesome websites');
insert into course(cnumber, cname, credits, cdesc) values('CMSC4133', 'Concepts of Artifical Intelligence', 3, 'Awesome class about awsome stuff');
insert into course(cnumber, cname, credits, cdesc) values('CMSC2423', 'Programming II', 3, 'Learning how to actually program');

insert into prereq(cnumber, prereq) values('CMSC4003', 'CMSC2423');
insert into prereq(cnumber, prereq) values('CMSC4003', 'CMSC3413');
insert into prereq(cnumber, prereq) values('CMSC3413', 'CMSC2423');
insert into prereq(cnumber, prereq) values('CMSC4063', 'CMSC4003');

insert into section(seqid, max_seat, avail_seat, time, cnumber, day, semester, year) values('10123', 30, 29, '5:15pm-8:15pm', 'CMSC4003', 'M', 'Fall', '2015');
insert into section(seqid, max_seat, avail_seat, time, cnumber, day, semester, year) values('10133', 30, 29, '6:00pm-9:00pm', 'CMSC4003', 'W', 'Fall', '2015');
insert into section(seqid, max_seat, avail_seat, time, cnumber, day, semester, year) values('10088', 20, 18, '9:00am-10:15am', 'CMSC2423', 'TR', 'Fall', '2015');
insert into section(seqid, max_seat, avail_seat, time, cnumber, day, semester, year) values('10144', 20, 0, '12:00pm-1:15pm', 'CMSC3413', 'MW', 'Fall', '2015');

insert into enroll(sid, seqid, grade) values('JS3', '10088', '2');
insert into enroll(sid, seqid, grade) values('MM1', '10088', '3');
insert into enroll(sid, seqid, grade) values('MM1', '10133', NULL);
insert into enroll(sid, seqid, grade) values('MK2', '10123', NULL);

commit;