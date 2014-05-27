SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `gamik` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `gamik` ;

-- -----------------------------------------------------
-- Table `gamik`.`User`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gamik`.`User` (
  `idUser` BIGINT NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(45) NULL,
  `password` VARCHAR(45) NULL,
  `title` VARCHAR(45) NULL,
  `name` VARCHAR(45) NULL,
  `surname` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `createCourse` INT NULL,
  PRIMARY KEY (`idUser`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gamik`.`Group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gamik`.`Group` (
  `idGroup` BIGINT NOT NULL AUTO_INCREMENT,
  `groupName` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idGroup`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gamik`.`Course`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gamik`.`Course` (
  `idCourse` BIGINT NOT NULL AUTO_INCREMENT,
  `description` TEXT NULL,
  `folderPath` VARCHAR(100) NULL,
  `templatePath` VARCHAR(100) NULL,
  PRIMARY KEY (`idCourse`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gamik`.`Lesson`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gamik`.`Lesson` (
  `idLesson` BIGINT NOT NULL AUTO_INCREMENT,
  `nr` INT NULL,
  `filePath` VARCHAR(100) NULL,
  `date` DATETIME NULL,
  `courseId` BIGINT NOT NULL,
  PRIMARY KEY (`idLesson`),
  INDEX `fk_Lesson_Course1_idx` (`courseId` ASC),
  CONSTRAINT `fk_Lesson_Course1`
    FOREIGN KEY (`courseId`)
    REFERENCES `gamik`.`Course` (`idCourse`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gamik`.`GroupLecturer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gamik`.`GroupLecturer` (
  `idGroupLecturer` BIGINT NOT NULL AUTO_INCREMENT,
  `roleId` INT NULL,
  `userId` BIGINT NOT NULL,
  PRIMARY KEY (`idGroupLecturer`),
  INDEX `fk_GroupLecturer_Users1_idx` (`userId` ASC),
  CONSTRAINT `fk_GroupLecturer_Users1`
    FOREIGN KEY (`userId`)
    REFERENCES `gamik`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gamik`.`GroupCourse`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gamik`.`GroupCourse` (
  `idGroupCourse` BIGINT NOT NULL AUTO_INCREMENT,
  `dataStart` DATETIME NULL,
  `dataStop` DATETIME NULL,
  `groupLecturerId` BIGINT NOT NULL,
  `courseId` BIGINT NOT NULL,
  `groupId` BIGINT NOT NULL,
  PRIMARY KEY (`idGroupCourse`),
  INDEX `fk_GroupCourse_GroupLecturer1_idx` (`groupLecturerId` ASC),
  INDEX `fk_GroupCourse_Course1_idx` (`courseId` ASC),
  INDEX `fk_GroupCourse_Group1_idx` (`groupId` ASC),
  CONSTRAINT `fk_GroupCourse_GroupLecturer1`
    FOREIGN KEY (`groupLecturerId`)
    REFERENCES `gamik`.`GroupLecturer` (`idGroupLecturer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_GroupCourse_Course1`
    FOREIGN KEY (`courseId`)
    REFERENCES `gamik`.`Course` (`idCourse`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_GroupCourse_Group1`
    FOREIGN KEY (`groupId`)
    REFERENCES `gamik`.`Group` (`idGroup`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gamik`.`Scores`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gamik`.`Scores` (
  `idScores` BIGINT NOT NULL AUTO_INCREMENT,
  `points` INT NULL,
  `awards` TEXT NULL,
  `achieve` TEXT NULL,
  `userId` BIGINT NOT NULL,
  `groupCourseId` BIGINT NOT NULL,
  PRIMARY KEY (`idScores`),
  INDEX `fk_Scores_Users1_idx` (`userId` ASC),
  INDEX `fk_Scores_GroupCourse1_idx` (`groupCourseId` ASC),
  CONSTRAINT `fk_Scores_Users1`
    FOREIGN KEY (`userId`)
    REFERENCES `gamik`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Scores_GroupCourse1`
    FOREIGN KEY (`groupCourseId`)
    REFERENCES `gamik`.`GroupCourse` (`idGroupCourse`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gamik`.`Content`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gamik`.`Content` (
  `idContent` BIGINT NOT NULL AUTO_INCREMENT,
  `content` TEXT NULL,
  `langId` INT NULL,
  PRIMARY KEY (`idContent`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `gamik`.`GroupUsers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gamik`.`GroupUsers` (
  `idGroupUsers` BIGINT NOT NULL AUTO_INCREMENT,
  `userId` BIGINT NOT NULL,
  `groupId` BIGINT NOT NULL,
  PRIMARY KEY (`idGroupUsers`),
  INDEX `fk_GroupUsers_Users1_idx` (`userId` ASC),
  INDEX `fk_GroupUsers_Group1_idx` (`groupId` ASC),
  CONSTRAINT `fk_GroupUsers_Users1`
    FOREIGN KEY (`userId`)
    REFERENCES `gamik`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_GroupUsers_Group1`
    FOREIGN KEY (`groupId`)
    REFERENCES `gamik`.`Group` (`idGroup`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
