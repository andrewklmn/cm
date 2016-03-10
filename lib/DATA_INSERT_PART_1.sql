SET FOREIGN_KEY_CHECKS=0;

insert into Roles (RoleId, RoleName, RoleLabel, PasswordMinLength) values
(1,'admin', 'администратор', 12);
insert into Roles (RoleId, RoleName, RoleLabel, PasswordMinLength) values
(2,'supervisor', 'контролёр', 6);
insert into Roles (RoleId, RoleName, RoleLabel, PasswordMinLength) values
(3,'operator', 'оператор', 6);
insert into Roles (RoleId, RoleName, RoleLabel, PasswordMinLength) values
(4,'security', 'инспектор по безопасности', 12);
insert into Roles (RoleId, RoleName, RoleLabel, PasswordMinLength) values
(5,'developer', 'разработчик', 3);


SET FOREIGN_KEY_CHECKS=0;
insert into UserConfiguration (UserFamilyName, UserFirstName, UserPatronymic,
UserPost, UserRoleId, UserCreateDate, UserLogin, UserPassword, UserSalt, LoggingAttemptsLimit, BadLogAttempts, 
UserIsBlocked, UserLogicallyDeleted, ChangePassword, LastChangePasswordDate, ValidDays, InterfaceLanguage,
OldUserFamilyName, OldUserFirstName, OldUserPatronymic, OldUserPost, GenetiveName, OldGenetiveName, InstrName, OldInstrName)
values ('Гвоздев', 'Дмитрий', 'Сергеевич', 'администратор системы', 1, 
'2013-01-01 00:00:00', 'dmitry', MD5('123'), 'iuy', 5, 0, 0, 0, 0, '2014-03-01 13:00:00', 1201, 
'Russian', 'Гвоздев', 'Дмитрий', 'Сергеевич', 'администратор системы','Гвоздева','Гвоздева','Гвоздевым','Гвоздевым');

insert into UserConfiguration (UserFamilyName, UserFirstName, UserPatronymic,
UserPost, UserRoleId, UserCreateDate, UserLogin, UserPassword, UserSalt, LoggingAttemptsLimit, BadLogAttempts, 
UserIsBlocked, UserLogicallyDeleted, ChangePassword, LastChangePasswordDate, ValidDays, InterfaceLanguage,
OldUserFamilyName, OldUserFirstName, OldUserPatronymic, OldUserPost, GenetiveName, OldGenetiveName, InstrName, OldInstrName)
values ('Клименюк', 'Андрей', 'Юрьевич', 'администратор системы', 1, 
'2013-01-01 00:00:00', 'andrew', MD5('123'), 'iuy', 5, 0, 
0, 0, 0, '2014-03-01 13:00:00', 1201, 'English', 'Клименюк', 'Андрей', 'Юрьевич','администратор системы','Клименюка','Клименюка','Клименюком','Клименюком');

insert into UserConfiguration (UserFamilyName, UserFirstName, UserPatronymic,
UserPost, UserRoleId, UserCreateDate, UserLogin, UserPassword, UserSalt, LoggingAttemptsLimit, BadLogAttempts, 
UserIsBlocked, UserLogicallyDeleted, ChangePassword, LastChangePasswordDate, ValidDays, CashRoomId, InterfaceLanguage, 
Phone, CurrentScenario,
OldUserFamilyName, OldUserFirstName, OldUserPatronymic, OldUserPost, OldPhone, GenetiveName, OldGenetiveName, InstrName, OldInstrName)
values ('Александров', 'Егор', 'Викторович', 'контролёр', 2, 
'2013-01-01 00:00:00', 'egor', MD5('123'), 'iuy', 5, 0, 
0, 0, 0, '2014-03-01 13:00:00', 1201, 1, 'Russian','+79213123133', 1,
'Александров', 'Егор', 'Викторович', 'контролёр', '+79213123133','Александрова','Александрова','Александровым','Александровым');

insert into UserConfiguration (UserFamilyName, UserFirstName, UserPatronymic,
UserPost, UserRoleId, UserCreateDate, UserLogin, UserPassword, UserSalt, LoggingAttemptsLimit, BadLogAttempts, 
UserIsBlocked, UserLogicallyDeleted, ChangePassword,  LastChangePasswordDate, ValidDays, CashRoomId, InterfaceLanguage,
Phone, CurrentScenario, OldUserFamilyName, OldUserFirstName, OldUserPatronymic, OldUserPost, OldPhone, GenetiveName, OldGenetiveName, InstrName, OldInstrName)
values ('Черенкова', 'Светлана', 'Николаевна', 'старший кассир', 
3, '2013-01-01 00:00:00', 'sveta', MD5('123'), 'iuy', 5, 0, 
0, 0, 0, '2014-03-01 13:00:00', 1201, 1, 'Russian', '+79144882061', 1,
'Черенкова', 'Светлана', 'Николаевна', 'старший кассир', '+79144882061','Черенковой','Черенковой','Черенковой','Черенковой');

insert into UserConfiguration (UserFamilyName, UserFirstName, UserPatronymic,
UserPost, UserRoleId, UserCreateDate, UserLogin, UserPassword, UserSalt, LoggingAttemptsLimit, BadLogAttempts, 
UserIsBlocked, UserLogicallyDeleted, ChangePassword, LastChangePasswordDate, ValidDays, CashRoomId, InterfaceLanguage,
Phone, CurrentScenario, OldUserFamilyName, OldUserFirstName, OldUserPatronymic, OldUserPost, OldPhone, GenetiveName, OldGenetiveName, InstrName, OldInstrName)
values ('Андрианова', 'Юлия', 'Вячеславовна', 'кассир', 
3, '2013-01-01 00:00:00', 'julia', MD5('123'), 'iuy', 5, 0, 
0, 0, 0, '2014-03-01 13:00:00', 1201, 1, 'Russian', '+79032370995', 1,
'Андрианова', 'Юлия', 'Вячеславовна', 'кассир', '+79032370995','Андриановой','Андриановой','Андриановой','Андриановой');

insert into UserConfiguration (UserFamilyName, UserFirstName, UserPatronymic,
UserPost, UserRoleId, UserCreateDate, UserLogin, UserPassword, UserSalt, LoggingAttemptsLimit, BadLogAttempts, 
UserIsBlocked, UserLogicallyDeleted, ChangePassword, LastChangePasswordDate, ValidDays, InterfaceLanguage,
OldUserFamilyName, OldUserFirstName, OldUserPatronymic, OldUserPost, GenetiveName, OldGenetiveName, InstrName, OldInstrName)
values ('Бабаев', 'Александр', 'Александрович', 'администратор по безопасности', 
4, '2013-01-01 00:00:00', 'alex', MD5('123'), 'iuy', 5, 0, 0, 0, 0, '2014-03-01 13:00:00',
1201, 'Russian','Бабаев','Александр','Александрович','администратор по безопасности','Бабаева','Бабаева','Бабаевым','Бабаевым');

insert into UserConfiguration (UserFamilyName, UserFirstName, UserPatronymic,
UserPost, UserRoleId, UserCreateDate, UserLogin, UserPassword, UserSalt, LoggingAttemptsLimit, BadLogAttempts, 
UserIsBlocked, UserLogicallyDeleted, ChangePassword, LastChangePasswordDate, ValidDays, InterfaceLanguage)
values ('Разработчик', 'Р', 'Р', 'разработчик', 
5, '2013-03-01 00:00:00', 'developer', MD5('123'), 'iuy', 10, 0, 
0, 0, 0, '2014-01-01 13:00:00', 1201, 'English');

insert into UserConfiguration (UserFamilyName, UserFirstName, UserPatronymic,
UserPost, UserRoleId, UserCreateDate, UserLogin, UserPassword, UserSalt, LoggingAttemptsLimit, BadLogAttempts, 
UserIsBlocked, UserLogicallyDeleted, ChangePassword, LastChangePasswordDate, ValidDays, CashRoomId, InterfaceLanguage,
Phone, CurrentScenario, OldUserFamilyName, OldUserFirstName, OldUserPatronymic, OldUserPost, OldPhone, GenetiveName, OldGenetiveName, InstrName, OldInstrName)
values ('Никифорова', 'Ирина', 'Геннадьевна', 'контролёр', 2, '2013-01-01 00:00:00', 'ira', MD5('123'), 'iuy',
5, 0, 0, 0, 0, '2014-01-01 13:00:00', 1201, 1, 'Russian', '+380679488367', 1,
'Никифорова', 'Ирина', 'Геннадьевна', 'контролёр', '+380679488367','Никифоровой','Никифоровой','Никифоровой','Никифоровой');

insert into UsersIP (UserId, IP) values (1, '%');
insert into UsersIP (UserId, IP) values (2, '%');
insert into UsersIP (UserId, IP) values (3, '%');
insert into UsersIP (UserId, IP) values (4, '%');
insert into UsersIP (UserId, IP) values (5, '%');
insert into UsersIP (UserId, IP) values (6, '%');
insert into UsersIP (UserId, IP) values (7, '%');
insert into UsersIP (UserId, IP) values (8, '%');


INSERT INTO SorterTypes (SorterTypeId, SorterType) VALUES (1, 'FS-2000');
INSERT INTO SorterTypes (SorterTypeId, SorterType) VALUES (2, 'Cobra');
INSERT INTO SorterTypes (SorterTypeId, SorterType) VALUES (3, 'Glory');

INSERT INTO CashRooms (CashRoomName) VALUES ('№ 1');
INSERT INTO CashRooms (CashRoomName) VALUES ('№ 2');

insert into DepositIndex (IndexValue, IndexLabel) values (3, 'годные');
insert into DepositIndex (IndexValue, IndexLabel) values (4, 'ветхие');
insert into DepositIndex (IndexValue, IndexLabel) values (5, 'в упаковке кредитных организаций');

insert into SorterIndexes (IndexName, DepositIndexId) values ('Index_3', 1);
insert into SorterIndexes (IndexName, DepositIndexId) values ('Index_4', 2);
insert into SorterIndexes (IndexName, DepositIndexId) values ('Index_5', 3);
insert into SorterIndexes (IndexName, DepositIndexId) values ('инд. 3', 1);
insert into SorterIndexes (IndexName, DepositIndexId) values ('инд. 4', 2);
insert into SorterIndexes (IndexName, DepositIndexId) values ('инд. 5', 3);


insert into Machines (SorterName, SorterTypeId, SorterVariant, Softwarerelease, CashRoomId) values
('0007', 1 , 'FS-2000', '1', 1);

insert into Machines (SorterName, SerialNumber, SorterTypeId, SorterVariant, Softwarerelease, CashRoomId,
NetworkAddress, NetworkMask, NetworkPort, MachineLogin, MachinePass, MachineDatabaseName) values
('151', '400840', 2, 'Cobra-4004', '17.005', 1,
'11.0.0.2', '255.255.255.0', '1433', 'sa', '', 'TransDBSQL');

insert into Machines (SorterName, SerialNumber, SorterTypeId, SorterVariant, Softwarerelease, CashRoomId) values
('152', '400679', 2, 'Cobra-4004', '17.003', 1);

insert into Machines (SorterName, SerialNumber, SorterTypeId, SorterVariant, Softwarerelease, CashRoomId) values
('251', '400680', 2, 'Cobra-4004', '17.003', 1);

insert into Machines (SorterName, SerialNumber, SorterTypeId, SorterVariant, Softwarerelease,
NetworkAddress, NetworkMask, NetworkPort, MachineLogin, MachinePass, MachineDatabaseName,
MachineConnectionDirectory, CashRoomId) values
('Simulator', '1', 2, 'Simulator', '0.1', '127.0.0.1', '255.255.255.0', 3306, 'simulator',
'123456', 'simulator', '/var/www/html/cashmaster/simulator', 1);


insert into Grades (GradeName, GradeLabel) values ('SUSPECT', 'Сомнит.');
insert into Grades (GradeName, GradeLabel) values ('FIT', 'Годные');
insert into Grades (GradeName, GradeLabel) values ('UNFIT', 'Ветхие');
insert into Grades (GradeName, GradeLabel) values ('COUNT', 'Счёт');
insert into Grades (GradeName, GradeLabel) values ('SHRED', 'Уничтож.');
INSERT INTO Grades (GradeName, GradeLabel) VALUES ('MOD1997', '1997');
INSERT INTO Grades (GradeName, GradeLabel) VALUES ('MOD2004', 'Мод.2004');
INSERT INTO Grades (GradeName, GradeLabel) VALUES ('MOD2010', 'Мод.2010');



insert into Currency (CurrSymbol, CurrCode, CurrYear, CurrName, CurrSign, SeriaLength, NumberLength) 
values ('RUB', 643, 1997, 'Российский рубль', '', 2, 7);
insert into Currency (CurrSymbol, CurrCode, CurrName, CurrSign, SeriaLength, NumberLength) 
values ('USD', 840, 'доллар США','$', 2, 9);


insert into Denoms values (1, 0.01, 1, '1 копейка');
insert into Denoms values (2, 0.05, 1, '5 копеек');
insert into Denoms values (3, 0.1, 1, '10 копеек');
insert into Denoms values (4, 0.5, 1, '50 копеек');
insert into Denoms values (5, 1, 1, '1 рубль');
insert into Denoms values (6, 2, 1, '2 рубля');
insert into Denoms values (7, 5, 1, '5 рублей');
insert into Denoms values (8, 10, 1, '10 рублей');
insert into Denoms values (9, 50, 1, '50 рублей');
insert into Denoms values (10, 100, 1, '100 рублей');
insert into Denoms values (11, 500, 1, '500 рублей');
insert into Denoms values (12, 1000, 1, '1000 рублей');
insert into Denoms values (13, 5000, 1, '5000 рублей');
insert into Denoms values (14, 1, 2, '1 доллар');
insert into Denoms values (15, 2, 2, '2 доллара');
insert into Denoms values (16, 5, 2, '5 долларов');
insert into Denoms values (17, 10, 2, '10 долларов');
insert into Denoms values (18, 20, 2, '20 долларов');
insert into Denoms values (19, 50, 2, '50 долларов');
insert into Denoms values (20, 100, 2, '100 долларов');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile,Description, FileName) 
values ('Акт формы 0402145', 0, 0, 0,'Акт о расхождении (отдельный акт для каждого расхождения)', 'printed_0402145.php');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile,Description,FileName) 
values ('Акт формы 0402145 обобщённый', 0, 0, 0,'Акт о расхождении (все типы расхождений депозита в одном акте)', 'printed_0402145_complex.php');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile,FileName) 
values ('Справка об обработанных банкнотах, форма 0402013', 0, 1, 0,'printed_0402013.php');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile, Description,FileName) 
values ('Отчётная информация аппаратно-программного комплекса', 0, 1, 0, 'Выводит данные 
по классам ГОДНЫЕ, ВЕТХИЕ, УНИЧТОЖЕННЫЕ в с группировкой по машинам, индексам, номиналам для одной валюты,
а также суммарные данные и данные о кассовых просчётах','general_apk_printed.php');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile, Description,FileName) 
values ('Подробная сводная информация об обработке ценностей', 0, 1, 0, 'Выводит данные пересчёта в
разрезе машин, операторов сверки, а также обобщённые данные и данные о кассовых
просчётах. Осуществляет разбивку по всем использованным классам.','general_rollup_printed.php');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile, Description,FileName) 
values ('Отчёт по клиентам', 0, 0, 0, 'Данные пересчётов в разрезе клиентов с разбивкой по
всем использованным классам, с указанием недостач и излишков.','customer_rollup_printed.php');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile,Description,FileName) 
values ('RECRESULT', 0, 0, 1,'XML-файл','recresult.php');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile,Description,FileName) 
values ('RECRESULT (expanded mode)', 0, 0, 1,'XML-файл','recresult_ext.php');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile,Description,FileName) 
values ('AKT0402198', 0, 0, 1,'XML-файл','akt_0402198.php');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile,Description,FileName) 
values ('AKT0402145', 0, 0, 1,'XML-файл. Отдельный для каждого вида расхождения в депозите','akt_0402145.php');

insert into ReportTypes (ReportLabel, DefaultReport, NeedSigner, GenerateXmlFile,Description,FileName) 
values ('C', 0, 1, 1,'XML-файл для АС ДНДО','c.php');


insert into ValuableTypes (ValuableTypeName, ValuableTypeLabel, SerialNumberIsUsed) 
values ('coins', 'монеты', 0);
insert into ValuableTypes (ValuableTypeName, ValuableTypeLabel, SerialNumberIsUsed) 
values ('banknotes', 'банкноты', 1);

insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2, N'10 Roubles (M97) FIT',  8, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2, N'10 Roubles (M04) FIT Cfolds',  8, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'10 Roubles (M04) FIT',  8, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'10 Roubles (M97) Shredded',  8, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'10 Roubles (M97) FIT Cfolds',  8, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'10 Roubles (M97) UNFIT MD',  8, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'10 Roubles (M97) UNFIT',  8, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'10 Roubles (M04) UNFIT MD',  8, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'10 Roubles (M04) UNFIT',  8, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'10 Roubles (M04) Shredded',  8, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Roubles (M97) FIT Cfolds',  9, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Roubles (M97) FIT',  9, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Roubles (M04) FIT Cfolds',  9, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Roubles (M04) FIT',  9, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Roubles (M97) Shredded',  9, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Roubles (M97) UNFIT MD',  9, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Roubles (M97) UNFIT',  9, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Roubles (M04) UNFIT MD',  9, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Roubles (M04) UNFIT',  9, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Roubles (M04) Shredded',  9, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Roubles (M97) FIT Cfolds',  10, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Roubles (M97) FIT',  10, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Roubles (M04) FIT Cfolds',  10, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Roubles (M04) FIT',  10, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Roubles (M97) Shredded',  10, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Roubles (M97) UNFIT MD',  10, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Roubles (M97) UNFIT',  10, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Roubles (M04) UNFIT MD',  10, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Roubles (M04) UNFIT',  10, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Roubles (M04) Shredded',  10, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M97) FIT Cfolds',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M97) FIT',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M04) FIT Cfolds',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M04) FIT',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (10) FIT Cfolds',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (10) Fit',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M97) Shredded',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M10) Shredded',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M97) UNFIT MD',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M97) UNFIT',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M04) UNFIT MD',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M04) UNFIT',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (10) Unfit MD',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (10) Unfit',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'500 Roubles (M04) Shredded',  11, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M97) FIT Cfolds',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M97) FIT',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M04) FIT Cfolds',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M04) FIT',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M10) FIT Cfolds',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M10) FIT',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M04) Shredded',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M10) Shredded',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M97) UNFIT MD',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M97) UNFIT',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M04) UNFIT MD',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M04) UNFIT',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M10) UNFIT MD',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M10) UNFIT',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1000 Roubles (M97) Shredded',  12, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5000 Roubles (10) FIT Cfolds',  13, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5000 Roubles (10) FIT',  13, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5000 Roubles (M04) Shredded',  13, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5000 Roubles (M10) Shredded',  13, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5000 Roubles (M04) FIT Cfolds',  13, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5000 Roubles (M04) UNFIT MD',  13, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5000 Roubles (M04) FIT',  13, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5000 Roubles (M04) UNFIT',  13, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5000 Roubles (10) Unfit MD',  13, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5000 Roubles (10) Unfit',  13, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'1 Usd Count',  14, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'2 Usd Count',  15, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'5 Usd Count',  16, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'10 Usd Count',  17, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'20 Usd Count',  18, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'50 Usd Count',  19, 2);
insert into Valuables (SorterTypeId, CategoryName, DenomId, ValuableTypeId) values
(2,N'100 Usd Count',  20, 2);


insert into SystemGlobals (CashCenterName, CashCenterCity, CashCenterCode, KPCode, OKATOCode, 
ComplexName, DefaultLanguage, AllowRecBySupervisor, FastenUserToIp, UseGenitiveName) values
('МРХ г. С.-Петербург ЦХ Банка России', 'Санкт-Петербург',
044083002,
43,
43,
'АПК на базе ССМ Кобра-4004', 'Russian', 0, 1, 1);


insert into UserHashHistory (UserId, oldHash) values (1, MD5('123'));
insert into UserHashHistory (UserId, oldHash) values (2, MD5('123'));
insert into UserHashHistory (UserId, oldHash) values (3, MD5('123'));
insert into UserHashHistory (UserId, oldHash) values (4, MD5('123'));
insert into UserHashHistory (UserId, oldHash) values (5, MD5('123'));
insert into UserHashHistory (UserId, oldHash) values (6, MD5('123'));
insert into UserHashHistory (UserId, oldHash) values (7, MD5('123'));
insert into UserHashHistory (UserId, oldHash) values (8, MD5('123'));

insert into ExternalUsers (ExternalUserPost, ExternalUserName) values 
('Старший контролёр-кассир', 'С.И.Павлова');
insert into ExternalUsers (ExternalUserPost, ExternalUserName) values 
('Заведующий кладовой', 'С.Н. Сафарова');
insert into ExternalUsers (ExternalUserPost, ExternalUserName) values 
('Контролёр кладовой', 'П.С. Щербакова');

insert into Customers (CustomerName, CustomerCode, CustomerKPCode,
CustomerOKATOCode, CustomerAddress, Customer_Email1,
CustomerPhone1, CustomerContactName, CustomerContactPost, LogicallyDeleted)
values
('Королевский Банк Шотландии', 853946577, '79', '12', 'Англия, Лондон', 'bank@email.com',
'12345678', 'Nick Simper', 'директор', 0);

insert into Customers (CustomerName, CustomerCode, CustomerKPCode,
CustomerOKATOCode, CustomerAddress, Customer_Email1,
CustomerPhone1, CustomerContactName, CustomerContactPost, LogicallyDeleted)
values
('Банк Возрождение', 777809145, '78', '79', 'Город Сергиев ПОсад', 'bank2@email.com',
'12345678', 'Ирина Петровна', 'Директор', 0);

insert into Customers (CustomerName, CustomerCode, CustomerKPCode,
CustomerOKATOCode, CustomerAddress, Customer_Email1,
CustomerPhone1, CustomerContactName, CustomerContactPost, LogicallyDeleted)
values
('Сбербанк РОссии, мОСКВОРЕЦКОЕ ОТД. №66', 846547665, '15', '66', 'г. Москва, ул. Панфилова, 57', 'sberbank2@email.com',
'495 666-44-66', 'Пухов. А.А.', 'начальник', 0);

insert into Customers (CustomerName, CustomerCode, CustomerKPCode,
CustomerOKATOCode, CustomerAddress, Customer_Email1,
CustomerPhone1, CustomerContactName, CustomerContactPost, LogicallyDeleted)
values
('Сбербанк России, Сокольническое отд. №88', 846547543, '15', '22', 'г. Москва, пр. Труда, 181', 'sberbank5@email.com',
'495 567-54-11', 'Бухарин П.П.', 'Зам. начальника', 0);

insert into Customers (CustomerName, CustomerCode, CustomerKPCode,
CustomerOKATOCode, CustomerAddress, Customer_Email1,
CustomerPhone1, CustomerContactName, CustomerContactPost, LogicallyDeleted)
values
('Сбербанк России, Семёновское отд. № 33', 846547765, '10', '60', 'пгт. Семёновка, ул. Ленина, 1', 'sberbank8@email.com',
'4323-78-45-36', 'Дыбенко У.Е.', 'Начальник', 0);

insert into Customers (CustomerName, CustomerCode, CustomerKPCode,
CustomerOKATOCode, CustomerAddress, Customer_Email1,
CustomerPhone1, CustomerContactName, CustomerContactPost, LogicallyDeleted)
values ('Сбербанк России, Кировское отд. № 5, г. Санкт-Петербург', 
937594267, 40, 32, 'Санкт-Петербург. пр. Стачек, д.7, литера Б', 'sberbank8@email.com',
'812-635-45-45', 'Грек. Р.С.', 'начальник', 0);


INSERT INTO `Scenario` VALUES (1,'Однономинальная сортировка банкнот на ГОДНЫЕ и ВЕТХИЕ',0,1000,1,1,1,1,1,0,1,0,0,1,1,1,1,1,1,1,1,0),(2,'Однономинальная сортировка банкнот с уничтожением ветхих банкнот',0,1000,0,0,1,1,1,0,0,0,0,1,0,1,1,1,1,1,1,0),(3,'Сортировка банкнот по модификациям выпуска',1,1000,1,0,1,1,1,0,0,0,1,0,0,1,0,0,0,1,1,0),(4,'Мультивалютный мультиноминальный пересчёт RUB и USD',0,1000,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,0,0,0);

INSERT INTO `ScenValuableTypes` VALUES (1,1,2,1),(2,2,2,1),(3,3,2,1),(4,4,2,1),(5,1,1,0),(6,2,1,0),(7,4,1,0),(8,3,1,0);

INSERT INTO `ScenDenoms` VALUES (1,1,7,2,1),(2,1,8,2,1),(3,1,9,2,1),(4,1,10,2,1),(5,1,11,2,1),(6,1,12,2,1),(7,1,13,2,1),(8,2,7,2,1),(9,2,8,2,1),(10,2,9,2,1),(11,2,10,2,1),(12,2,11,2,1),(13,2,12,2,1),(14,2,13,2,1),(15,3,7,2,1),(16,3,8,2,1),(17,3,9,2,1),(18,3,10,2,1),(19,3,11,2,1),(20,3,12,2,1),(21,3,13,2,1),(22,4,14,2,1),(23,4,15,2,1),(24,4,16,2,1),(25,4,17,2,1),(26,4,18,2,1),(27,4,19,2,1),(28,4,20,2,1),(29,4,7,2,1),(30,4,8,2,1),(31,4,9,2,1),(32,4,10,2,1),(33,4,11,2,1),(34,4,12,2,1),(35,4,13,2,1),(36,1,1,2,0),(37,1,2,2,0),(38,1,3,2,0),(39,1,4,2,0),(40,1,5,2,0),(41,1,6,2,0),(42,1,14,2,0),(43,1,15,2,0),(44,1,16,2,0),(45,1,17,2,0),(46,1,18,2,0),(47,1,19,2,0),(48,1,20,2,0),(49,2,1,2,0),(50,2,2,2,0),(51,2,3,2,0),(52,2,4,2,0),(53,2,5,2,0),(54,2,6,2,0),(55,2,14,2,0),(56,2,15,2,0),(57,2,16,2,0),(58,2,17,2,0),(59,2,18,2,0),(60,2,19,2,0),(61,2,20,2,0),(62,4,1,2,0),(63,4,2,2,0),(64,4,3,2,0),(65,4,4,2,0),(66,4,5,2,0),(67,4,6,2,0),(68,3,1,2,0),(69,3,2,2,0),(70,3,3,2,0),(71,3,4,2,0),(72,3,5,2,0),(73,3,6,2,0),(74,3,14,2,0),(75,3,15,2,0),(76,3,16,2,0),(77,3,17,2,0),(78,3,18,2,0),(79,3,19,2,0),(80,3,20,2,0);

INSERT INTO `ScenSorterGrades` VALUES (1,1,2,2,1),(2,1,3,2,1),(3,2,2,2,1),(4,2,5,2,1),(5,3,6,2,1),(6,3,7,2,1),(7,3,8,2,1),(8,4,4,2,1),(9,1,7,2,0),(10,1,8,2,0),(11,1,6,2,0),(12,1,1,2,0),(13,1,4,2,0),(14,1,5,2,0),(15,2,3,2,0),(16,2,7,2,0),(17,2,8,2,0),(18,2,6,2,0),(19,2,1,2,0),(20,2,4,2,0),(21,4,3,2,0),(22,4,2,2,0),(23,4,7,2,0),(24,4,8,2,0),(25,4,6,2,0),(26,4,1,2,0),(27,4,5,2,0),(28,3,3,2,0),(29,3,2,2,0),(30,3,1,2,0),(31,3,4,2,0),(32,3,5,2,0);

INSERT INTO `ScenReconGrades` VALUES (1,1,1,2,1),(2,1,2,2,1),(3,1,3,2,1),(4,2,1,2,1),(5,2,2,2,1),(6,2,3,2,1),(7,3,1,2,1),(8,3,6,2,1),(9,3,7,2,1),(10,3,8,2,1),(11,4,1,2,1),(12,4,4,2,1),(13,1,7,2,0),(14,1,8,2,0),(15,1,6,2,0),(16,1,4,2,0),(17,1,5,2,0),(18,2,7,2,0),(19,2,8,2,0),(20,2,6,2,0),(21,2,4,2,0),(22,2,5,2,0),(23,4,3,2,0),(24,4,2,2,0),(25,4,7,2,0),(26,4,8,2,0),(27,4,6,2,0),(28,4,5,2,0),(29,3,3,2,0),(30,3,2,2,0),(31,3,4,2,0),(32,3,5,2,0);

insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (1, 1,1);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (1, 2,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (1, 3,1);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (1, 4,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (1, 5,1);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (1, 6,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (1, 7,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (1, 8,1);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (1, 9,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (1, 10,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (2, 1,1);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (2, 2,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (2, 3,1);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (2, 4,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (2, 5,1);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (2, 6,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (2, 7,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (2, 8,8);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (2, 9,0);
insert into ScenReportTypes (ScenarioId, ReportTypeId, IsUsed) values (2, 10,0);


insert into DiscrepancyKind (DiscrepancyKindName) values ('сомнительная банкнота');
insert into DiscrepancyKind (DiscrepancyKindName) values ('недостача');
insert into DiscrepancyKind (DiscrepancyKindName) values ('излишек');


INSERT INTO `ValuablesGrades` VALUES (1,1,1,2),(2,1,2,2),(3,1,3,2),(4,1,5,2),(5,1,11,2),(6,1,12,2),(7,1,13,2),(8,1,14,2),(9,1,21,2),(10,1,22,2),(11,1,23,2),(12,1,24,2),(13,1,31,2),(14,1,32,2),(15,1,33,2),(16,1,34,2),(17,1,35,2),(18,1,36,2),(19,1,46,2),(20,1,47,2),(21,1,48,2),(22,1,49,2),(23,1,50,2),(24,1,51,2),(25,1,61,2),(26,1,62,2),(27,1,65,2),(28,1,67,2),(29,1,6,3),(30,1,7,3),(31,1,8,3),(32,1,9,3),(33,1,16,3),(34,1,17,3),(35,1,18,3),(36,1,19,3),(37,1,26,3),(38,1,27,3),(39,1,28,3),(40,1,29,3),(41,1,39,3),(42,1,40,3),(43,1,41,3),(44,1,42,3),(45,1,43,3),(46,1,44,3),(47,1,54,3),(48,1,55,3),(49,1,56,3),(50,1,57,3),(51,1,58,3),(52,1,59,3),(53,1,66,3),(54,1,68,3),(55,1,69,3),(56,1,70,3),(57,2,1,2),(58,2,2,2),(59,2,3,2),(60,2,5,2),(61,2,11,2),(62,2,12,2),(63,2,13,2),(64,2,14,2),(65,2,21,2),(66,2,22,2),(67,2,23,2),(68,2,24,2),(69,2,31,2),(70,2,32,2),(71,2,33,2),(72,2,34,2),(73,2,35,2),(74,2,36,2),(75,2,46,2),(76,2,47,2),(77,2,48,2),(78,2,49,2),(79,2,50,2),(80,2,51,2),(81,2,61,2),(82,2,62,2),(83,2,65,2),(84,2,67,2),(85,2,4,5),(86,2,10,5),(87,2,15,5),(88,2,20,5),(89,2,25,5),(90,2,30,5),(91,2,37,5),(92,2,38,5),(93,2,45,5),(94,2,52,5),(95,2,53,5),(96,2,60,5),(97,2,63,5),(98,2,64,5),(99,3,1,6),(100,3,4,6),(101,3,5,6),(102,3,6,6),(103,3,7,6),(104,3,11,6),(105,3,12,6),(106,3,15,6),(107,3,16,6),(108,3,17,6),(109,3,21,6),(110,3,22,6),(111,3,25,6),(112,3,26,6),(113,3,27,6),(114,3,31,6),(115,3,32,6),(116,3,39,6),(117,3,40,6),(118,3,37,6),(119,3,46,6),(120,3,47,6),(121,3,54,6),(122,3,55,6),(123,3,60,6),(124,3,2,7),(125,3,3,7),(126,3,8,7),(127,3,9,7),(128,3,10,7),(129,3,13,7),(130,3,14,7),(131,3,18,7),(132,3,19,7),(133,3,20,7),(134,3,23,7),(135,3,24,7),(136,3,28,7),(137,3,29,7),(138,3,30,7),(139,3,33,7),(140,3,34,7),(141,3,41,7),(142,3,42,7),(143,3,45,7),(144,3,48,7),(145,3,49,7),(146,3,52,7),(147,3,56,7),(148,3,57,7),(149,3,63,7),(150,3,65,7),(151,3,66,7),(152,3,67,7),(153,3,68,7),(154,3,35,8),(155,3,36,8),(156,3,38,8),(157,3,43,8),(158,3,44,8),(159,3,50,8),(160,3,51,8),(161,3,53,8),(162,3,58,8),(163,3,59,8),(164,3,61,8),(165,3,62,8),(166,3,64,8),(167,3,69,8),(168,3,70,8),(169,4,1,4),(170,4,2,4),(171,4,3,4),(172,4,4,4),(173,4,5,4),(174,4,6,4),(175,4,7,4),(176,4,8,4),(177,4,9,4),(178,4,10,4),(179,4,11,4),(180,4,12,4),(181,4,13,4),(182,4,14,4),(183,4,15,4),(184,4,16,4),(185,4,17,4),(186,4,18,4),(187,4,19,4),(188,4,20,4),(189,4,21,4),(190,4,22,4),(191,4,23,4),(192,4,24,4),(193,4,25,4),(194,4,26,4),(195,4,27,4),(196,4,28,4),(197,4,29,4),(198,4,30,4),(199,4,31,4),(200,4,32,4),(201,4,33,4),(202,4,34,4),(203,4,35,4),(204,4,36,4),(205,4,37,4),(206,4,38,4),(207,4,39,4),(208,4,40,4),(209,4,41,4),(210,4,42,4),(211,4,43,4),(212,4,44,4),(213,4,45,4),(214,4,46,4),(215,4,47,4),(216,4,48,4),(217,4,49,4),(218,4,50,4),(219,4,51,4),(220,4,52,4),(221,4,52,4),(222,4,54,4),(223,4,55,4),(224,4,56,4),(225,4,57,4),(226,4,58,4),(227,4,59,4),(228,4,60,4),(229,4,61,4),(230,4,62,4),(231,4,63,4),(232,4,64,4),(233,4,65,4),(234,4,66,4),(235,4,67,4),(236,4,68,4),(237,4,69,4),(238,4,70,4),(239,4,71,4),(240,4,72,4),(241,4,73,4),(242,4,74,4),(243,4,75,4),(244,4,76,4),(245,4,77,4),(246,1,10,0),(247,1,4,0),(248,1,30,0),(249,1,25,0),(250,1,52,0),(251,1,53,0),(252,1,60,0),(253,1,20,0),(254,1,15,0),(255,1,45,0),(256,1,38,0),(257,1,37,0),(258,1,63,0),(259,1,64,0),(260,2,9,0),(261,2,8,0),(262,2,7,0),(263,2,6,0),(264,2,29,0),(265,2,28,0),(266,2,27,0),(267,2,26,0),(268,2,57,0),(269,2,56,0),(270,2,59,0),(271,2,58,0),(272,2,55,0),(273,2,54,0),(274,2,19,0),(275,2,18,0),(276,2,17,0),(277,2,16,0),(278,2,44,0),(279,2,43,0),(280,2,42,0),(281,2,41,0),(282,2,40,0),(283,2,39,0),(284,2,70,0),(285,2,69,0),(286,2,68,0),(287,2,66,0),(288,4,53,4);


