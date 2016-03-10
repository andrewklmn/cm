SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `cashmaster` ;
CREATE SCHEMA IF NOT EXISTS `cashmaster` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `cashmaster` ;

-- -----------------------------------------------------
-- Table `cashmaster`.`SystemGlobals`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`SystemGlobals` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`SystemGlobals` (
  `SystemGlobalsId` BIGINT NOT NULL AUTO_INCREMENT ,
  `CashCenterName` VARCHAR(80) NOT NULL ,
  `CashCenterCity` VARCHAR(45) NOT NULL ,
  `CashCenterCode` VARCHAR(30) NOT NULL ,
  `KPCode` VARCHAR(30) NULL ,
  `OKATOCode` VARCHAR(30) NULL ,
  `ComplexName` VARCHAR(80) NULL ,
  `DefaultLanguage` VARCHAR(45) NOT NULL DEFAULT 'English' ,
  `AllowRecBySupervisor` TINYINT NOT NULL DEFAULT 0 ,
  `FilesCorrupted` TINYINT NOT NULL DEFAULT 0 ,
  `CheckOperDay` TINYINT NOT NULL DEFAULT 0 ,
  `OperDayStatus` TINYINT NOT NULL DEFAULT 0 ,
  `AutoArchivePeriod` INT NOT NULL DEFAULT 14 ,
  `LeaveDataFor` INT NOT NULL DEFAULT 14 ,
  `ServiceMode` TINYINT NOT NULL DEFAULT 0 ,
  `LastArchiveDate` DATETIME NOT NULL DEFAULT '1972-11-29 00:00:00' ,
  `FastenUserToIp` TINYINT NOT NULL DEFAULT 0 ,
  `UseGenitiveName` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`SystemGlobalsId`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Currency`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Currency` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Currency` (
  `CurrencyId` BIGINT NOT NULL AUTO_INCREMENT ,
  `CurrSymbol` VARCHAR(4) NOT NULL ,
  `CurrCode` VARCHAR(4) NOT NULL ,
  `CurrYear` VARCHAR(8) NULL ,
  `CurrName` VARCHAR(30) NOT NULL ,
  `CurrSign` VARCHAR(1) NULL ,
  `SeriaLength` INT NOT NULL DEFAULT 2 ,
  `NumberLength` INT NOT NULL DEFAULT 7 ,
  PRIMARY KEY (`CurrencyId`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Denoms`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Denoms` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Denoms` (
  `DenomId` BIGINT NOT NULL AUTO_INCREMENT ,
  `Value` DECIMAL(10,2) NOT NULL ,
  `CurrencyId` BIGINT NOT NULL ,
  `DenomLabel` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`DenomId`) ,
  INDEX `Currency_CurrencyId_Denoms_idx` (`CurrencyId` ASC) ,
  CONSTRAINT `Currency_CurrencyId_Denoms`
    FOREIGN KEY (`CurrencyId` )
    REFERENCES `cashmaster`.`Currency` (`CurrencyId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`SorterTypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`SorterTypes` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`SorterTypes` (
  `SorterTypeId` BIGINT NOT NULL AUTO_INCREMENT ,
  `SorterType` VARCHAR(24) NOT NULL ,
  PRIMARY KEY (`SorterTypeId`) ,
  UNIQUE INDEX `SorterType_UNIQUE` (`SorterType` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ValuableTypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ValuableTypes` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ValuableTypes` (
  `ValuableTypeId` BIGINT NOT NULL AUTO_INCREMENT ,
  `ValuableTypeName` VARCHAR(18) NOT NULL ,
  `ValuableTypeLabel` VARCHAR(24) NOT NULL ,
  `SerialNumberIsUsed` TINYINT NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`ValuableTypeId`) ,
  UNIQUE INDEX `ValuableTypeName_UNIQUE` (`ValuableTypeName` ASC) ,
  UNIQUE INDEX `ValuableTypeLabel_UNIQUE` (`ValuableTypeLabel` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Valuables`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Valuables` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Valuables` (
  `ValuableId` BIGINT NOT NULL AUTO_INCREMENT ,
  `CategoryName` VARCHAR(50) NOT NULL ,
  `SorterTypeId` BIGINT NOT NULL ,
  `DenomId` BIGINT NOT NULL ,
  `ValuableTypeId` BIGINT NOT NULL ,
  PRIMARY KEY (`ValuableId`) ,
  INDEX `Valuables_DenomId_Denoms_idx` (`DenomId` ASC) ,
  INDEX `Valuables_SorterType_Machines_idx` (`SorterTypeId` ASC) ,
  INDEX `Valuables_ValuableTypeId_ValuableTypes_idx` (`ValuableTypeId` ASC) ,
  UNIQUE INDEX `valuable_name_unique` (`CategoryName` ASC, `SorterTypeId` ASC) ,
  CONSTRAINT `Valuables_DenomId_Denoms`
    FOREIGN KEY (`DenomId` )
    REFERENCES `cashmaster`.`Denoms` (`DenomId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Valuables_SorterType_Machines`
    FOREIGN KEY (`SorterTypeId` )
    REFERENCES `cashmaster`.`SorterTypes` (`SorterTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Valuables_ValuableTypeId_ValuableTypes`
    FOREIGN KEY (`ValuableTypeId` )
    REFERENCES `cashmaster`.`ValuableTypes` (`ValuableTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Roles` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Roles` (
  `RoleId` BIGINT NOT NULL AUTO_INCREMENT ,
  `RoleName` VARCHAR(24) NOT NULL ,
  `RoleLabel` VARCHAR(45) NULL ,
  `PasswordMinLength` INT NOT NULL DEFAULT 6 ,
  PRIMARY KEY (`RoleId`) ,
  UNIQUE INDEX `RoleName_UNIQUE` (`RoleName` ASC) );


-- -----------------------------------------------------
-- Table `cashmaster`.`CashRooms`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`CashRooms` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`CashRooms` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `CashRoomName` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Scenario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Scenario` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Scenario` (
  `ScenarioId` BIGINT NOT NULL AUTO_INCREMENT ,
  `ScenarioName` VARCHAR(120) NOT NULL ,
  `BlindReconciliation` TINYINT NOT NULL DEFAULT 0 COMMENT 'Deprecated' ,
  `DefExpectedNumber` INT NOT NULL DEFAULT 1000 ,
  `UsePackIntegrity` TINYINT NOT NULL DEFAULT 1 ,
  `UseStrapsIntegrity` TINYINT NOT NULL DEFAULT 1 ,
  `UseSealNumber` TINYINT NOT NULL DEFAULT 1 ,
  `UseSealType` TINYINT NOT NULL DEFAULT 1 ,
  `SingleDenomDeposits` TINYINT NOT NULL DEFAULT 1 ,
  `ReconcileAgainstValue` TINYINT NOT NULL DEFAULT 0 ,
  `DefaultScenario` TINYINT NOT NULL DEFAULT 0 ,
  `LogicallyDeleted` TINYINT NOT NULL DEFAULT 0 ,
  `ForceDepositDetails` TINYINT NOT NULL DEFAULT 0 ,
  `UseStrapType` TINYINT NOT NULL DEFAULT 1 ,
  `UseSealIntegrity` TINYINT NOT NULL DEFAULT 1 ,
  `UsePackType` TINYINT NOT NULL DEFAULT 1 ,
  `UsePackId` TINYINT NOT NULL DEFAULT 1 ,
  `UseDepositPackingDate` TINYINT NOT NULL DEFAULT 1 ,
  `UsePackingOperatorName` TINYINT NOT NULL DEFAULT 1 ,
  `UseSuspectSerialNumbers` TINYINT NOT NULL DEFAULT 1 ,
  `CheckIndexes` TINYINT NOT NULL DEFAULT 1 ,
  `UsePreparationStep` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`ScenarioId`) ,
  UNIQUE INDEX `ScenarioName_UNIQUE` (`ScenarioName` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`UserConfiguration`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`UserConfiguration` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`UserConfiguration` (
  `UserId` BIGINT NOT NULL AUTO_INCREMENT ,
  `UserFamilyName` VARCHAR(24) NOT NULL ,
  `UserFirstName` VARCHAR(24) NOT NULL ,
  `UserPatronymic` VARCHAR(24) NULL ,
  `UserPost` VARCHAR(80) NOT NULL ,
  `UserRoleId` BIGINT NOT NULL ,
  `UserCreateDate` DATETIME NOT NULL ,
  `UserLogin` VARCHAR(16) NOT NULL ,
  `UserPassword` VARCHAR(32) NOT NULL ,
  `UserSalt` VARCHAR(32) NOT NULL ,
  `LoggingAttemptsLimit` INT NOT NULL DEFAULT 5 ,
  `BadLogAttempts` INT NOT NULL DEFAULT 0 ,
  `UserIsBlocked` TINYINT NOT NULL DEFAULT 0 ,
  `UserLogicallyDeleted` TINYINT NOT NULL DEFAULT 0 ,
  `ChangePassword` TINYINT NOT NULL DEFAULT 1 ,
  `LastChangePasswordDate` DATETIME NOT NULL ,
  `ValidDays` INT NOT NULL DEFAULT 180 ,
  `CreatedBy` BIGINT NULL ,
  `CashRoomId` BIGINT NOT NULL DEFAULT 0 ,
  `InterfaceLanguage` VARCHAR(45) NOT NULL DEFAULT 'English' ,
  `Phone` VARCHAR(45) NULL ,
  `OldUserFamilyName` VARCHAR(45) NULL ,
  `OldUserFirstName` VARCHAR(45) NULL ,
  `OldUserPatronymic` VARCHAR(45) NULL ,
  `OldUserPost` VARCHAR(80) NULL ,
  `OldPhone` VARCHAR(45) NULL ,
  `Blind` TINYINT NOT NULL DEFAULT 0 ,
  `CurrentScenario` BIGINT NULL ,
  `GenetiveName` VARCHAR(24) NULL ,
  `OldGenetiveName` VARCHAR(24) NULL ,
  `InstrName` VARCHAR(24) NULL ,
  `OldInstrName` VARCHAR(24) NULL ,
  PRIMARY KEY (`UserId`) ,
  INDEX `UserRoleId_RoleId_idx` (`UserRoleId` ASC) ,
  INDEX `UserConfiguration_CreatedBy_UserId_idx` (`CreatedBy` ASC) ,
  INDEX `UserConfiguration_CashRoomId_CashRooms_idx` (`CashRoomId` ASC) ,
  INDEX `UserConfiguration_CurrentScenario_Scenarios_idx` (`CurrentScenario` ASC) ,
  CONSTRAINT `UserRoleId_RoleId`
    FOREIGN KEY (`UserRoleId` )
    REFERENCES `cashmaster`.`Roles` (`RoleId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `UserConfiguration_CreatedBy_UserId`
    FOREIGN KEY (`CreatedBy` )
    REFERENCES `cashmaster`.`UserConfiguration` (`UserId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `UserConfiguration_CashRoomId_CashRooms`
    FOREIGN KEY (`CashRoomId` )
    REFERENCES `cashmaster`.`CashRooms` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `UserConfiguration_CurrentScenario_Scenarios`
    FOREIGN KEY (`CurrentScenario` )
    REFERENCES `cashmaster`.`Scenario` (`ScenarioId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Customers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Customers` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Customers` (
  `CustomerId` BIGINT NOT NULL AUTO_INCREMENT ,
  `CustomerName` VARCHAR(80) NOT NULL ,
  `CustomerCode` VARCHAR(30) NOT NULL ,
  `CustomerKPCode` VARCHAR(50) NULL ,
  `CustomerOKATOCode` VARCHAR(50) NULL ,
  `CustomerAddress` VARCHAR(120) NULL ,
  `Customer_Email1` VARCHAR(80) NULL ,
  `Customer_Email2` VARCHAR(80) NULL ,
  `CustomerPhone1` VARCHAR(20) NULL ,
  `CustomerPhone2` VARCHAR(20) NULL ,
  `CustomerContactName` VARCHAR(45) NULL ,
  `CustomerContactPost` VARCHAR(80) NULL ,
  `LogicallyDeleted` TINYINT NOT NULL DEFAULT 0 ,
  `CustomerCodeLength` INT NOT NULL DEFAULT 9 ,
  PRIMARY KEY (`CustomerId`) ,
  UNIQUE INDEX `CustomerCode_UNIQUE` (`CustomerCode` ASC) ,
  UNIQUE INDEX `CustomerName_UNIQUE` (`CustomerName` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`DepositIndex`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`DepositIndex` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`DepositIndex` (
  `DepositIndexId` BIGINT NOT NULL AUTO_INCREMENT ,
  `IndexValue` VARCHAR(16) NOT NULL ,
  `IndexLabel` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`DepositIndexId`) ,
  UNIQUE INDEX `IndexValue_UNIQUE` (`IndexValue` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`DepositRecs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`DepositRecs` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`DepositRecs` (
  `DepositRecId` BIGINT NOT NULL AUTO_INCREMENT ,
  `ScenarioId` BIGINT NOT NULL ,
  `CustomerId` BIGINT NULL ,
  `DepositPackingDate` DATE NULL ,
  `PackingOperatorName` VARCHAR(30) NULL ,
  `PackIntegrity` TINYINT NOT NULL DEFAULT 1 ,
  `StrapsIntegrity` TINYINT NOT NULL DEFAULT 1 ,
  `SealNumber` VARCHAR(30) NULL ,
  `SealType` TINYINT NOT NULL DEFAULT 1 ,
  `PackId` VARCHAR(30) NULL ,
  `RecOperatorId` BIGINT NOT NULL ,
  `RecCreateDatetime` DATETIME NOT NULL ,
  `RecLastChangeDatetime` TIMESTAMP NOT NULL ,
  `IsBalanced` TINYINT NOT NULL DEFAULT 0 ,
  `RecSupervisorId` BIGINT NULL ,
  `ReconcileStatus` TINYINT NOT NULL DEFAULT 0 ,
  `FwdToSupervisor` TINYINT NOT NULL DEFAULT 0 ,
  `StrapType` TINYINT NOT NULL DEFAULT 1 ,
  `SealIntegrity` TINYINT NOT NULL DEFAULT 1 ,
  `PackType` TINYINT NOT NULL DEFAULT 1 ,
  `DepositIndexId` BIGINT NOT NULL DEFAULT 0 ,
  `Reported` TINYINT NOT NULL DEFAULT 0 ,
  `ServiceRec` TINYINT NOT NULL DEFAULT 0 ,
  `CardNumber` VARCHAR(22) NULL ,
  `PrepOperatorId` BIGINT NULL ,
  PRIMARY KEY (`DepositRecId`) ,
  INDEX `DepositRecs_CustomerId_Customers_idx` (`CustomerId` ASC) ,
  INDEX `DepositRecs_UserId_Users_idx` (`RecOperatorId` ASC) ,
  INDEX `DepositRecs_ScenarioId_Scenario_idx` (`ScenarioId` ASC) ,
  INDEX `DepositRecs_UserId_UserConfiguration_idx` (`RecSupervisorId` ASC) ,
  INDEX `DepositRecs_DepositIndexId_DepositIndex_idx` (`DepositIndexId` ASC) ,
  INDEX `DepositRecs_PrepOperatorId_UserConfig_idx` (`PrepOperatorId` ASC) ,
  CONSTRAINT `DepositRecs_CustomerId_Customers`
    FOREIGN KEY (`CustomerId` )
    REFERENCES `cashmaster`.`Customers` (`CustomerId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `DepositRecs_UserId_Users`
    FOREIGN KEY (`RecOperatorId` )
    REFERENCES `cashmaster`.`UserConfiguration` (`UserId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `DepositRecs_ScenarioId_Scenario`
    FOREIGN KEY (`ScenarioId` )
    REFERENCES `cashmaster`.`Scenario` (`ScenarioId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `DepositRecs_UserId_UserConfiguration`
    FOREIGN KEY (`RecSupervisorId` )
    REFERENCES `cashmaster`.`UserConfiguration` (`UserId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `DepositRecs_DepositIndexId_DepositIndex`
    FOREIGN KEY (`DepositIndexId` )
    REFERENCES `cashmaster`.`DepositIndex` (`DepositIndexId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `DepositRecs_PrepOperatorId_UserConfig`
    FOREIGN KEY (`PrepOperatorId` )
    REFERENCES `cashmaster`.`UserConfiguration` (`UserId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Machines`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Machines` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Machines` (
  `MachineDBId` BIGINT NOT NULL AUTO_INCREMENT ,
  `SorterName` VARCHAR(22) NOT NULL ,
  `SerialNumber` VARCHAR(24) NULL ,
  `SorterTypeId` BIGINT NOT NULL DEFAULT 1 ,
  `SorterVariant` VARCHAR(24) NULL ,
  `Softwarerelease` VARCHAR(45) NULL ,
  `CashRoomId` BIGINT NOT NULL DEFAULT 1 ,
  `MachineLogicallyDeleted` TINYINT NULL DEFAULT 0 ,
  `NetworkAddress` VARCHAR(15) NULL DEFAULT '192.168.0.10' ,
  `NetworkMask` VARCHAR(15) NULL DEFAULT '255.255.255.0' ,
  `NetworkPort` INT NULL DEFAULT 3306 ,
  `MachineLogin` VARCHAR(50) NULL DEFAULT 'login' ,
  `MachinePass` VARCHAR(50) NULL DEFAULT 'password' ,
  `MachineDatabaseName` VARCHAR(50) NULL DEFAULT 'database' ,
  `MachineConnectionDirectory` VARCHAR(255) NULL DEFAULT '/' ,
  `MachineConnectionOn` TINYINT NULL DEFAULT 0 ,
  PRIMARY KEY (`MachineDBId`) ,
  UNIQUE INDEX `MachineName_UNIQUE` (`SorterName` ASC) ,
  INDEX `Machines_MachineType_SorterTypes_idx` (`SorterTypeId` ASC) ,
  INDEX `Machines_CashRoomId_CashRooms_idx` (`CashRoomId` ASC) ,
  CONSTRAINT `Machines_MachineType_SorterTypes`
    FOREIGN KEY (`SorterTypeId` )
    REFERENCES `cashmaster`.`SorterTypes` (`SorterTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Machines_CashRoomId_CashRooms`
    FOREIGN KEY (`CashRoomId` )
    REFERENCES `cashmaster`.`CashRooms` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`DepositRuns`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`DepositRuns` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`DepositRuns` (
  `DepositRunId` BIGINT NOT NULL AUTO_INCREMENT ,
  `DataSortCardNumber` VARCHAR(22) NOT NULL ,
  `MachineDBId` BIGINT NOT NULL ,
  `ShiftId` INT NOT NULL DEFAULT 1 ,
  `BatchId` INT NOT NULL DEFAULT 1 ,
  `DepositInBatchId` INT NULL DEFAULT 1 ,
  `DepositStartTimeStamp` DATETIME NOT NULL ,
  `DepositEndTimeStamp` DATETIME NOT NULL ,
  `OperatorName` VARCHAR(22) NOT NULL ,
  `SupervisorName` VARCHAR(22) NULL ,
  `IndexName` VARCHAR(22) NOT NULL ,
  `SortModeName` VARCHAR(50) NULL ,
  `DepositRecId` BIGINT NULL ,
  PRIMARY KEY (`DepositRunId`) ,
  INDEX `DepositRuns_DepositRecId_DepositRecs_idx` (`DepositRecId` ASC) ,
  INDEX `Machines_DB_idx` (`MachineDBId` ASC) ,
  CONSTRAINT `DepositRuns_DepositRecId_DepositRecs`
    FOREIGN KEY (`DepositRecId` )
    REFERENCES `cashmaster`.`DepositRecs` (`DepositRecId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Machines_DB`
    FOREIGN KEY (`MachineDBId` )
    REFERENCES `cashmaster`.`Machines` (`MachineDBId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`SorterAccountingData`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`SorterAccountingData` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`SorterAccountingData` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `DepositRunId` BIGINT NOT NULL ,
  `ValuableId` BIGINT NOT NULL ,
  `ActualCount` INT NOT NULL DEFAULT 0 ,
  INDEX `SorterAccountingData_DepositRuns_idx` (`DepositRunId` ASC) ,
  INDEX `SorterAccountingData_Valuables_idx` (`ValuableId` ASC) ,
  PRIMARY KEY (`Id`) ,
  CONSTRAINT `SorterAccountingData_DepositRuns`
    FOREIGN KEY (`DepositRunId` )
    REFERENCES `cashmaster`.`DepositRuns` (`DepositRunId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `SorterAccountingData_Valuables`
    FOREIGN KEY (`ValuableId` )
    REFERENCES `cashmaster`.`Valuables` (`ValuableId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Grades`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Grades` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Grades` (
  `GradeId` BIGINT NOT NULL AUTO_INCREMENT ,
  `GradeName` VARCHAR(7) NOT NULL ,
  `GradeLabel` VARCHAR(30) NOT NULL ,
  PRIMARY KEY (`GradeId`) ,
  UNIQUE INDEX `GradeName_UNIQUE` (`GradeName` ASC) ,
  UNIQUE INDEX `GradeLabel_UNIQUE` (`GradeLabel` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ReconAccountingData`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ReconAccountingData` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ReconAccountingData` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `DepositRecId` BIGINT NOT NULL ,
  `DenomId` BIGINT NOT NULL ,
  `GradeId` BIGINT NOT NULL ,
  `CullCount` INT NOT NULL DEFAULT 0 ,
  `ValuableTypeId` BIGINT NOT NULL ,
  INDEX `ReconAccountingData_DepositRecs_idx` (`DepositRecId` ASC) ,
  PRIMARY KEY (`id`) ,
  INDEX `ReconAccountingData_DenomId_Denoms_idx` (`DenomId` ASC) ,
  INDEX `ReconAccountingData_GradeId_Grades_idx` (`GradeId` ASC) ,
  INDEX `ReconAccountingData_ValuableTypeId_ValuableTypes_idx` (`ValuableTypeId` ASC) ,
  CONSTRAINT `ReconAccountingData_DepositRecs`
    FOREIGN KEY (`DepositRecId` )
    REFERENCES `cashmaster`.`DepositRecs` (`DepositRecId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ReconAccountingData_DenomId_Denoms`
    FOREIGN KEY (`DenomId` )
    REFERENCES `cashmaster`.`Denoms` (`DenomId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ReconAccountingData_GradeId_Grades`
    FOREIGN KEY (`GradeId` )
    REFERENCES `cashmaster`.`Grades` (`GradeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ReconAccountingData_ValuableTypeId_ValuableTypes`
    FOREIGN KEY (`ValuableTypeId` )
    REFERENCES `cashmaster`.`ValuableTypes` (`ValuableTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`DiscrepancyKind`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`DiscrepancyKind` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`DiscrepancyKind` (
  `DiscrepancyKindId` BIGINT NOT NULL AUTO_INCREMENT ,
  `DiscrepancyKindName` VARCHAR(25) NOT NULL ,
  PRIMARY KEY (`DiscrepancyKindId`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Acts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Acts` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Acts` (
  `ActId` BIGINT NOT NULL AUTO_INCREMENT ,
  `DepositId` BIGINT NOT NULL ,
  `DiscrepancyKindId` BIGINT NOT NULL ,
  `DiscrepancyDescr` VARCHAR(256) NULL ,
  PRIMARY KEY (`ActId`) ,
  INDEX `Acts_DepositId_DepositRecs_idx` (`DepositId` ASC) ,
  INDEX `Acts_DiscrepancyKindId_DiscrepancyKind_idx` (`DiscrepancyKindId` ASC) ,
  CONSTRAINT `Acts_DepositId_DepositRecs`
    FOREIGN KEY (`DepositId` )
    REFERENCES `cashmaster`.`DepositRecs` (`DepositRecId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Acts_DiscrepancyKindId_DiscrepancyKind`
    FOREIGN KEY (`DiscrepancyKindId` )
    REFERENCES `cashmaster`.`DiscrepancyKind` (`DiscrepancyKindId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`DepositCurrencyTotal`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`DepositCurrencyTotal` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`DepositCurrencyTotal` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `DepositRecId` BIGINT NOT NULL ,
  `CurrencyId` BIGINT NOT NULL ,
  `ExpectedDepositValue` DECIMAL(12,2) NOT NULL DEFAULT 0 ,
  `ValuableTypeId` BIGINT NOT NULL ,
  INDEX `DepositCurrencyTotal_DepositRecId_DepositRecs_idx` (`DepositRecId` ASC) ,
  INDEX `DepositCurrencyTotal_CurrencyId_Currency_idx` (`CurrencyId` ASC) ,
  PRIMARY KEY (`Id`) ,
  INDEX `DepositCurrencyTotal_ValuableTypeId_ValuableTypes_idx` (`ValuableTypeId` ASC) ,
  CONSTRAINT `DepositCurrencyTotal_DepositRecId_DepositRecs`
    FOREIGN KEY (`DepositRecId` )
    REFERENCES `cashmaster`.`DepositRecs` (`DepositRecId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `DepositCurrencyTotal_CurrencyId_Currency`
    FOREIGN KEY (`CurrencyId` )
    REFERENCES `cashmaster`.`Currency` (`CurrencyId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `DepositCurrencyTotal_ValuableTypeId_ValuableTypes`
    FOREIGN KEY (`ValuableTypeId` )
    REFERENCES `cashmaster`.`ValuableTypes` (`ValuableTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`DepositDenomTotal`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`DepositDenomTotal` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`DepositDenomTotal` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `DepositReclId` BIGINT NOT NULL ,
  `DenomId` BIGINT NOT NULL ,
  `ExpectedCount` INT NOT NULL DEFAULT 0 ,
  `ValuableTypeId` BIGINT NOT NULL ,
  INDEX `DepositDenomTotal_DepositRecId_DepositRecs_idx` (`DepositReclId` ASC) ,
  INDEX `DepositDenomTotal_DenomId_Denoms_idx` (`DenomId` ASC) ,
  PRIMARY KEY (`id`) ,
  INDEX `DepositDenomTotal_ValuableTypeId_ValuableTypes_idx` (`ValuableTypeId` ASC) ,
  CONSTRAINT `DepositDenomTotal_DepositRecId_DepositRecs`
    FOREIGN KEY (`DepositReclId` )
    REFERENCES `cashmaster`.`DepositRecs` (`DepositRecId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `DepositDenomTotal_DenomId_Denoms`
    FOREIGN KEY (`DenomId` )
    REFERENCES `cashmaster`.`Denoms` (`DenomId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `DepositDenomTotal_ValuableTypeId_ValuableTypes`
    FOREIGN KEY (`ValuableTypeId` )
    REFERENCES `cashmaster`.`ValuableTypes` (`ValuableTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ScenSorterGrades`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ScenSorterGrades` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ScenSorterGrades` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `ScenarioId` BIGINT NOT NULL ,
  `GradeId` BIGINT NOT NULL ,
  `ValuableTypeId` BIGINT NOT NULL ,
  `IsUsed` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`Id`) ,
  INDEX `ScenSorterGrades_scenarioId_Scenario_idx` (`ScenarioId` ASC) ,
  INDEX `ScenSorterGrades_GradeId_Grades_idx` (`GradeId` ASC) ,
  INDEX `ScenSorterGrades_ValuableTypeId_ValuableTypes_idx` (`ValuableTypeId` ASC) ,
  CONSTRAINT `ScenSorterGrades_scenarioId_Scenario`
    FOREIGN KEY (`ScenarioId` )
    REFERENCES `cashmaster`.`Scenario` (`ScenarioId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ScenSorterGrades_GradeId_Grades`
    FOREIGN KEY (`GradeId` )
    REFERENCES `cashmaster`.`Grades` (`GradeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ScenSorterGrades_ValuableTypeId_ValuableTypes`
    FOREIGN KEY (`ValuableTypeId` )
    REFERENCES `cashmaster`.`ValuableTypes` (`ValuableTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ScenReconGrades`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ScenReconGrades` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ScenReconGrades` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `ScenarioId` BIGINT NOT NULL ,
  `GradeId` BIGINT NOT NULL ,
  `ValuableTypeId` BIGINT NOT NULL ,
  `IsUsed` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`Id`) ,
  INDEX `ScenReconGrades_ScenarioId_Scenarios_idx` (`ScenarioId` ASC) ,
  INDEX `ScenReconGrades_Grades_GradeId_idx` (`GradeId` ASC) ,
  INDEX `ScenReconGrades_ValuableTypeId_ValuableTypes_idx` (`ValuableTypeId` ASC) ,
  CONSTRAINT `ScenReconGrades_ScenarioId_Scenarios`
    FOREIGN KEY (`ScenarioId` )
    REFERENCES `cashmaster`.`Scenario` (`ScenarioId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ScenReconGrades_Grades_GradeId`
    FOREIGN KEY (`GradeId` )
    REFERENCES `cashmaster`.`Grades` (`GradeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ScenReconGrades_ValuableTypeId_ValuableTypes`
    FOREIGN KEY (`ValuableTypeId` )
    REFERENCES `cashmaster`.`ValuableTypes` (`ValuableTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ScenDenoms`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ScenDenoms` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ScenDenoms` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `ScenarioId` BIGINT NOT NULL ,
  `DenomId` BIGINT NOT NULL ,
  `ValuableTypeId` BIGINT NOT NULL ,
  `IsUsed` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`Id`) ,
  INDEX `ScenDenoms_ScenarioId_Scenarios_idx` (`ScenarioId` ASC) ,
  INDEX `ScenDenoms_DenomId_Denoms_idx` (`DenomId` ASC) ,
  INDEX `ScenDenoms_ValuableTypeId_ValuableTypes_idx` (`ValuableTypeId` ASC) ,
  CONSTRAINT `ScenDenoms_ScenarioId_Scenarios`
    FOREIGN KEY (`ScenarioId` )
    REFERENCES `cashmaster`.`Scenario` (`ScenarioId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ScenDenoms_DenomId_Denoms`
    FOREIGN KEY (`DenomId` )
    REFERENCES `cashmaster`.`Denoms` (`DenomId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ScenDenoms_ValuableTypeId_ValuableTypes`
    FOREIGN KEY (`ValuableTypeId` )
    REFERENCES `cashmaster`.`ValuableTypes` (`ValuableTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ValuablesGrades`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ValuablesGrades` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ValuablesGrades` (
  `SequenceId` BIGINT NOT NULL AUTO_INCREMENT ,
  `ScenarioId` BIGINT NOT NULL ,
  `ValuableId` BIGINT NOT NULL ,
  `GradeId` BIGINT NOT NULL ,
  PRIMARY KEY (`SequenceId`) ,
  INDEX `ValuableGrades_ScenarioId_Scenarios_idx` (`ScenarioId` ASC) ,
  INDEX `ValuableGrades_GradeId_Grades_idx` (`GradeId` ASC) ,
  INDEX `ValuableGrades_ValuableId_Valuables_idx` (`ValuableId` ASC) ,
  CONSTRAINT `ValuableGrades_ScenarioId_Scenarios`
    FOREIGN KEY (`ScenarioId` )
    REFERENCES `cashmaster`.`Scenario` (`ScenarioId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ValuableGrades_GradeId_Grades`
    FOREIGN KEY (`GradeId` )
    REFERENCES `cashmaster`.`Grades` (`GradeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ValuableGrades_ValuableId_Valuables`
    FOREIGN KEY (`ValuableId` )
    REFERENCES `cashmaster`.`Valuables` (`ValuableId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Rejects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Rejects` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Rejects` (
  `RejectId` BIGINT NOT NULL AUTO_INCREMENT ,
  `RejectName` VARCHAR(50) NOT NULL ,
  `SorterTypeId` BIGINT NOT NULL ,
  `RejectMappingId` BIGINT NULL DEFAULT 0 ,
  `RejectDescription` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`RejectId`) ,
  INDEX `Rejects_SorterTypeId_SorterTypes_idx` (`SorterTypeId` ASC) ,
  CONSTRAINT `Rejects_SorterTypeId_SorterTypes`
    FOREIGN KEY (`SorterTypeId` )
    REFERENCES `cashmaster`.`SorterTypes` (`SorterTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`SorterRejectData`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`SorterRejectData` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`SorterRejectData` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `DepositRunId` BIGINT NOT NULL ,
  `RejectId` BIGINT NOT NULL ,
  `CullCount` INT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`Id`) ,
  INDEX `SorterRejectData_DepositRunId_DepositRuns_idx` (`DepositRunId` ASC) ,
  INDEX `SorterRejectData_RejectId_Rejects_idx` (`RejectId` ASC) ,
  CONSTRAINT `SorterRejectData_DepositRunId_DepositRuns`
    FOREIGN KEY (`DepositRunId` )
    REFERENCES `cashmaster`.`DepositRuns` (`DepositRunId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `SorterRejectData_RejectId_Rejects`
    FOREIGN KEY (`RejectId` )
    REFERENCES `cashmaster`.`Rejects` (`RejectId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ReportTypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ReportTypes` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ReportTypes` (
  `ReportTypeId` BIGINT NOT NULL AUTO_INCREMENT ,
  `ReportLabel` VARCHAR(80) NOT NULL ,
  `DefaultReport` TINYINT NOT NULL DEFAULT 0 ,
  `NeedSigner` TINYINT NOT NULL DEFAULT 0 ,
  `GenerateXmlFile` TINYINT NOT NULL DEFAULT 0 ,
  `Description` VARCHAR(300) NULL ,
  `FileName` VARCHAR(45) NULL ,
  PRIMARY KEY (`ReportTypeId`) ,
  UNIQUE INDEX `ReportLabel_UNIQUE` (`ReportLabel` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ScenReportTypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ScenReportTypes` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ScenReportTypes` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `ScenarioId` BIGINT NOT NULL ,
  `ReportTypeId` BIGINT NOT NULL ,
  `IsUsed` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`Id`) ,
  INDEX `ScenReports_ScenarioId_Scenario_idx` (`ScenarioId` ASC) ,
  INDEX `ScenReports_ReportId_Reports_idx` (`ReportTypeId` ASC) ,
  CONSTRAINT `ScenReports_ScenarioId_Scenario`
    FOREIGN KEY (`ScenarioId` )
    REFERENCES `cashmaster`.`Scenario` (`ScenarioId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ScenReportTypes_ReportTypeId_ReportTypes`
    FOREIGN KEY (`ReportTypeId` )
    REFERENCES `cashmaster`.`ReportTypes` (`ReportTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ExternalUsers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ExternalUsers` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ExternalUsers` (
  `ExternalUserId` BIGINT NOT NULL AUTO_INCREMENT ,
  `ExternalUserPost` VARCHAR(80) NOT NULL ,
  `ExternalUserName` VARCHAR(45) NOT NULL ,
  `Phone` VARCHAR(45) NULL ,
  PRIMARY KEY (`ExternalUserId`) ,
  UNIQUE INDEX `unique_name_post` (`ExternalUserPost` ASC, `ExternalUserName` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`SuspectSerialNumbs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`SuspectSerialNumbs` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`SuspectSerialNumbs` (
  `SequenceId` BIGINT NOT NULL AUTO_INCREMENT ,
  `DepositRecId` BIGINT NOT NULL ,
  `DenomId` BIGINT NOT NULL ,
  `LeftSeria` VARCHAR(5) NULL ,
  `LeftNumber` VARCHAR(10) NULL ,
  `RightSeria` VARCHAR(5) NULL ,
  `RightNumber` VARCHAR(10) NULL ,
  PRIMARY KEY (`SequenceId`) ,
  INDEX `NoteSerialNumber_DepositRecId_DepositRecs_idx` (`DepositRecId` ASC) ,
  INDEX `SuspectSerialNumbs_DenomId_Denoms_idx` (`DenomId` ASC) ,
  CONSTRAINT `SuspectSerialNumber_DepositRecId_DepositRecs`
    FOREIGN KEY (`DepositRecId` )
    REFERENCES `cashmaster`.`DepositRecs` (`DepositRecId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `SuspectSerialNumbs_DenomId_Denoms`
    FOREIGN KEY (`DenomId` )
    REFERENCES `cashmaster`.`Denoms` (`DenomId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`SorterIndexes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`SorterIndexes` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`SorterIndexes` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `IndexName` VARCHAR(22) NOT NULL ,
  `DepositIndexId` BIGINT NOT NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `SorterIndexes_DepositIndexId_DepositIndex_idx` (`DepositIndexId` ASC) ,
  UNIQUE INDEX `IndexName_UNIQUE` (`IndexName` ASC) ,
  CONSTRAINT `SorterIndexes_DepositIndexId_DepositIndex`
    FOREIGN KEY (`DepositIndexId` )
    REFERENCES `cashmaster`.`DepositIndex` (`DepositIndexId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ReportSets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ReportSets` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ReportSets` (
  `SetId` BIGINT NOT NULL AUTO_INCREMENT ,
  `SetDateTime` DATETIME NOT NULL ,
  `CreatedBy` BIGINT NOT NULL ,
  `CashRoomId` BIGINT NOT NULL ,
  PRIMARY KEY (`SetId`) ,
  INDEX `ReportPacks_UserId_UserConfiguration_idx` (`CreatedBy` ASC) ,
  INDEX `ReportPacks_CashRoomId_CashRooms_idx` (`CashRoomId` ASC) ,
  CONSTRAINT `ReportSets_UserId_UserConfiguration`
    FOREIGN KEY (`CreatedBy` )
    REFERENCES `cashmaster`.`UserConfiguration` (`UserId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ReportSets_CashRoomId_CashRooms`
    FOREIGN KEY (`CashRoomId` )
    REFERENCES `cashmaster`.`CashRooms` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`Reports`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Reports` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`Reports` (
  `SeqId` BIGINT NOT NULL AUTO_INCREMENT ,
  `ReportTypeId` BIGINT NOT NULL ,
  `ReportSetId` BIGINT NOT NULL ,
  PRIMARY KEY (`SeqId`) ,
  INDEX `ReportLastNumber_ReportId_Reports_idx` (`ReportTypeId` ASC) ,
  INDEX `Reports_ReportPackId_ReportPacks_idx` (`ReportSetId` ASC) ,
  CONSTRAINT `Reports_ReportTypeId_ReportTypes`
    FOREIGN KEY (`ReportTypeId` )
    REFERENCES `cashmaster`.`ReportTypes` (`ReportTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Reports_ReportSetId_ReportPacks`
    FOREIGN KEY (`ReportSetId` )
    REFERENCES `cashmaster`.`ReportSets` (`SetId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`SystemLog`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`SystemLog` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`SystemLog` (
  `Id` BIGINT NULL AUTO_INCREMENT COMMENT 'Event number (refer to the file)' ,
  `DateAndTime` TIMESTAMP NOT NULL ,
  `Comment` VARCHAR(254) NOT NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`UserHashHistory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`UserHashHistory` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`UserHashHistory` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `UserId` BIGINT NOT NULL ,
  `OldHash` VARCHAR(32) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `UserHashHistory_UserId_UserConfiguration_idx` (`UserId` ASC) ,
  CONSTRAINT `UserHashHistory_UserId_UserConfiguration`
    FOREIGN KEY (`UserId` )
    REFERENCES `cashmaster`.`UserConfiguration` (`UserId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ScenValuableTypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ScenValuableTypes` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ScenValuableTypes` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `ScenarioId` BIGINT NOT NULL ,
  `ValuableTypeId` BIGINT NOT NULL ,
  `IsUsed` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`Id`) ,
  INDEX `ScenarioValuableTypes_ScenarioId_Scenarios_idx` (`ScenarioId` ASC) ,
  INDEX `ScenarioValuableTypes_ValuableTypeId_ValuableTypes_idx` (`ValuableTypeId` ASC) ,
  CONSTRAINT `ScenValuableTypes_ScenarioId_Scenarios`
    FOREIGN KEY (`ScenarioId` )
    REFERENCES `cashmaster`.`Scenario` (`ScenarioId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ScenValuableTypes_ValuableTypeId_ValuableTypes`
    FOREIGN KEY (`ValuableTypeId` )
    REFERENCES `cashmaster`.`ValuableTypes` (`ValuableTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ReportSignes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ReportSignes` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ReportSignes` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `RepSeqId` BIGINT NOT NULL ,
  `SignerId` BIGINT NOT NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `SignerLog_ReportLog_SeqId_idx` (`RepSeqId` ASC) ,
  INDEX `SignerLog_Signers_SignerId_idx` (`SignerId` ASC) ,
  CONSTRAINT `SignerLog_ReportLog_SeqId`
    FOREIGN KEY (`RepSeqId` )
    REFERENCES `cashmaster`.`Reports` (`SeqId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `SignerLog_Signers_SignerId`
    FOREIGN KEY (`SignerId` )
    REFERENCES `cashmaster`.`ExternalUsers` (`ExternalUserId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`ReportSaves`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`ReportSaves` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`ReportSaves` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `ReportSetId` BIGINT NOT NULL DEFAULT 0 ,
  `ReportTypeId` BIGINT NOT NULL DEFAULT 0 ,
  `Key` VARCHAR(50) NULL ,
  `Value` VARCHAR(150) NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `ReportSets_ReportSetId_ReportSets_idx` (`ReportSetId` ASC) ,
  INDEX `ReportSets_ReportTypeId_REportTypes_idx` (`ReportTypeId` ASC) ,
  CONSTRAINT `ReportSets_ReportSetId_ReportSets`
    FOREIGN KEY (`ReportSetId` )
    REFERENCES `cashmaster`.`ReportSets` (`SetId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ReportSets_ReportTypeId_REportTypes`
    FOREIGN KEY (`ReportTypeId` )
    REFERENCES `cashmaster`.`ReportTypes` (`ReportTypeId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`UsersIP`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`UsersIP` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`UsersIP` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `UserId` BIGINT NOT NULL ,
  `IP` VARCHAR(16) NOT NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `UsersIP_UserId_UserConfiguration_idx` (`UserId` ASC) ,
  CONSTRAINT `UsersIP_UserId_UserConfiguration`
    FOREIGN KEY (`UserId` )
    REFERENCES `cashmaster`.`UserConfiguration` (`UserId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`IntegrityCheck`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`IntegrityCheck` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`IntegrityCheck` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `FileName` VARCHAR(255) NOT NULL ,
  `Hash` VARCHAR(32) NOT NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cashmaster`.`UpdateHistory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`UpdateHistory` ;

CREATE  TABLE IF NOT EXISTS `cashmaster`.`UpdateHistory` (
  `Id` BIGINT NOT NULL AUTO_INCREMENT ,
  `UpdateDateTime` DATETIME NOT NULL ,
  `UpdateFile` VARCHAR(256) NOT NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `cashmaster`.`Prebook`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cashmaster`.`Prebook`;
CREATE  TABLE IF NOT EXISTS `cashmaster`.`Prebook` (
  `Id` BIGINT(20) NOT NULL ,
  `PackId` VARCHAR(30) NOT NULL ,
  `Filename` VARCHAR(40) NOT NULL ,
  `CashroomId` BIGINT(20) NOT NULL ,
  `UserId` BIGINT(20) NOT NULL ,
  `CustomerApproved` TINYINT(4) NOT NULL DEFAULT '0' ,
  `Prepared` TINYINT(4) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`Id`) ,
  INDEX `prebook_cashroomid_ashrooms_idx` (`CashroomId` ASC) ,
  INDEX `preebook_userid_users_idx` (`UserId` ASC) ,
  CONSTRAINT `prebook_cashroomid_cashrooms`
    FOREIGN KEY (`CashroomId` )
    REFERENCES `cashmaster`.`Cashrooms` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `preebook_userid_users`
    FOREIGN KEY (`UserId` )
    REFERENCES `cashmaster`.`UserConfiguration` (`UserId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


USE `cashmaster` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
