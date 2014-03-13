Create table CMS_AuthLevels(
 AuthLevelsID int NOT NULL AUTO_INCREMENT,
 Level1 VarChar(16) NOT NULL,
 primary key (AuthLevelsID)
 );

 Create table CMS_Charities(
 CharityID int NOT NULL AUTO_INCREMENT,
 Name varchar(32) NOT NULL,
 CountyID int NOT NULL,
 Address1 varchar(32) NOT NULL,
 Address2 varchar(32) NOT NULL,
 Phone varchar(16) NOT NULL,
 DomainName varchar(32) NOT NULL,
 CharityNo varchar(10) NOT NULL,
 BIC varchar(8) NOT NULL,
 IBAN varchar(20)NOT NULL,
 primary key (CharityID)
 );
 
 

 Create table CMS_CharityLayout(
 CharityLayoutID int NOT NULL AUTO_INCREMENT,
 CharityID int NOT NULL,
 Color1 Char(6)NOT NULL,
 Color2 Char(6)NOT NULL,
 Color3 Char(6)NOT NULL,
 Logo Nvarchar(255)NOT NULL,
 primary key (CharityLayoutID)
 );
 
 
 
 Create table CMS_CharityPages(
 CharityPageID int NOT NULL AUTO_INCREMENT,
 CharityID int NOT NULL,
 PageID int NOT NULL,
 CustomTitle varchar(32)NOT NULL,
 primary key (CharityPageID)
 );
 


 Create table CMS_CharityUsers(
 CharityUsersID int NOT NULL AUTO_INCREMENT,
 CharityID int NOT NULL,
 UserID int NOT NULL,
 AuthLevelsID int NOT NULL,
 primary key (CharityUsersID)
 
);




Create table CMS_LostFounds(
LostFoundID int NOT NULL AUTO_INCREMENT,
CharityID int NOT NULL,
CreatorID int NOT NULL,
CreatedOn DateTime NOT NULL,
EditorID int ,
EditedOn DateTime,
Title varchar(32) NOT NULL,
SubTitle varchar(32),
LastSeen DateTime,
Name varchar(32),
Description varchar(255) NOT NULL,
Contact Varchar(32),
Number1 Varchar(15),
Email Varchar(47),
Details Varchar(255) NOT NULL,
Image1 blob,
Image2 blob,
isLost tinyint,
primary key (LostFoundID) 
);



Create table CMS_Events(
EventID int NOT NULL AUTO_INCREMENT,
CharityID int NOT NULL,
CreatorID int NOT NULL,
CreatedOn DateTime NOT NULL,
EditorID int ,
EditedOn DateTime,
Title varchar(32) NOT NULL,
SubTitle varchar(32),
Start1 Datetime,
End1 Datetime,
Name varchar(32),
Description varchar(255) NOT NULL,
Contact Varchar(32),
Number1 Varchar(15),
Email Varchar(47),
Location Varchar(93) NOT NULL,
Image1 blob,
Image2 blob,
primary key (EventID) 
);



Create table CMS_Pets(
PetID int NOT NULL AUTO_INCREMENT,
CharityID int NOT NULL,
CreatorID int NOT NULL,
CreatedOn DateTime NOT NULL,
EditorID int ,
EditedOn DateTime,
Title varchar(32) NOT NULL,
SubTitle varchar(32),
Name varchar(32),
Description varchar(255) NOT NULL,
Contact Varchar(32),
Number1 Varchar(15),
Email Varchar(47),
Location Varchar(93) NOT NULL,
Image1 blob,
Image2 blob,
isAdoptable tinyint,
primary key (PetID) 
);



Create table CMS_Counties(
CountyID int NOT NULL,
County varchar(16) NOT NULL,
primary key (CountyID)
);

Create table CMS_Donations(
DonationID int NOT NULL AUTO_INCREMENT,
CharityID int NOT NULL,
Message varchar(255) NOT NULL,
Amount Decimal(6,2) NOT NULL,
Timestamp1 DateTime NOT NULL,
PageID int NOT NULL,
ContentID int NOT NULL,
primary key (DonationID)

);



Create table CMS_Pages(
PageID int NOT NULL AUTO_INCREMENT,
Name varchar(32) NOT NULL,
FileName varchar(32) NOT NULL,
AuthLevelsID int NOT NULL,
primary key (PageID)
);



Create table CMS_ProposedCharities(
ProposedCharitiesID int NOT NULL AUTO_INCREMENT ,
Name varchar(32) NOT NULL,
Address1 varchar(32) NOT NULL,
Address2 varchar(32) NOT NULL,
Address3 varchar(32) NOT NULL,
Phone varchar(16) NOT NULL,
DomainName varchar(32) NOT NULL,
CharityNo varchar(10) NOT NULL,
BIC varchar(8) NOT NULL,
IBAN varchar(20) NOT NULL,
CreatorID int NOT NULL,
primary key (ProposedCharitiesID)
);



Create table CMS_Users(
UserID int NOT NULL AUTO_INCREMENT,
EmailAddress varchar(32)NOT NULL,
EncryptedPassword varchar(255)NOT NULL,
FirstName varchar(20) NOT NULL,
LastName varchar(20) NOT NULL,
Address1 varchar(32) NOT NULL,
Address2 varchar(32) NOT NULL,
CountyID int NOT NULL,
Phone varchar(16) NOT NULL,
AddedByID int NOT NULL,
Admini tinyint NOT NULL,
primary key (UserID)
);



Create table CMS_UsersPages(
UserPageID int NOT NULL AUTO_INCREMENT,
UserID int NOT NULL,
PageID int NOT NULL,
CharityID int NOT NULL,
primary key (UserPageID)
);



Create table CMS_Stories(
StoryID int NOT NULL AUTO_INCREMENT,
title varchar(32) NOT NULL,
CharityID INT NOT NULL ,
primary key (StoryID)
);




Create table CMS_AccessRequest(
UserID int not null,
PageID int not null,
CharityID int not null,
pending tinyint not null,
foreign key (UserID) references CMS_Users(UserID),
foreign key (PageID) references CMS_Pages(PageID),
foreign key (CharityID) references CMS_Charities(CharityID)
);



Create table CMS_Contents(
ContentID int NOT NULL AUTO_INCREMENT,
LostFound varchar(32) NOT NULL,
Event varchar(32) NOT NULL,
Pet varchar(32) NOT NULL,
LostFoundID int NOT NULL,
EventID int NOT NULL,
PetID int NOT NULL,
primary key (ContentID)
);




Create table CMS_StoryContents(
StoryContentID int NOT NULL AUTO_INCREMENT,
StoryID int NOT NULL,
ContentID int NOT NULL,
primary key (StoryContentID)
);





/*insert CMS_AuthLevels*/
insert into CMS_AuthLevels(AuthLevelsID,Level1) values(0,'everyone');
insert into CMS_AuthLevels(AuthLevelsID,Level1) values(1,'registeredUser');
insert into CMS_AuthLevels(AuthLevelsID,Level1) values(2,'CharityAdmin');
insert into CMS_AuthLevels(AuthLevelsID,Level1) values(3,'SiteAdmin');

/*insert CMS_Counties*/
insert into CMS_Counties(CountyID,County) values(1,'Antrim');
insert into CMS_Counties(CountyID,County) values(2,'Armagh');
insert into CMS_Counties(CountyID,County) values(3,'Carlow');
insert into CMS_Counties(CountyID,County) values(4,'Cavan');
insert into CMS_Counties(CountyID,County) values(5,'Clare');
insert into CMS_Counties(CountyID,County) values(6,'Cork');
insert into CMS_Counties(CountyID,County) values(7,'Donegal');
insert into CMS_Counties(CountyID,County) values(8,'Down');
insert into CMS_Counties(CountyID,County) values(9,'Dublin');
insert into CMS_Counties(CountyID,County) values(10,'Fermanagh');
insert into CMS_Counties(CountyID,County) values(11,'Galway');
insert into CMS_Counties(CountyID,County) values(12,'Kerry');
insert into CMS_Counties(CountyID,County) values(13,'Kildare');
insert into CMS_Counties(CountyID,County) values(14,'Kilkenny');
insert into CMS_Counties(CountyID,County) values(15,'Laois');
insert into CMS_Counties(CountyID,County) values(16,'Leitrim');
insert into CMS_Counties(CountyID,County) values(17,'Limerick');
insert into CMS_Counties(CountyID,County) values(18,'Londonderry');
insert into CMS_Counties(CountyID,County) values(19,'Longford');
insert into CMS_Counties(CountyID,County) values(20,'Louth');
insert into CMS_Counties(CountyID,County) values(21,'Mayo');
insert into CMS_Counties(CountyID,County) values(22,'Meath');
insert into CMS_Counties(CountyID,County) values(23,'Monaghan');
insert into CMS_Counties(CountyID,County) values(24,'Offaly');
insert into CMS_Counties(CountyID,County) values(25,'Roscommon');
insert into CMS_Counties(CountyID,County) values(26,'Sligo');
insert into CMS_Counties(CountyID,County) values(27,'Tipperary');
insert into CMS_Counties(CountyID,County) values(28,'Tyrone');
insert into CMS_Counties(CountyID,County) values(29,'Waterford');
insert into CMS_Counties(CountyID,County) values(30,'Westmeath');
insert into CMS_Counties(CountyID,County) values(31,'Wexford');
insert into CMS_Counties(CountyID,County) values(32,'Wicklow');













ALTER TABLE CMS_Charities
ADD FOREIGN KEY (CountyID)
REFERENCES CMS_Counties(CountyID);

AlTER TABLE CMS_CharityLayout
 ADD FOREIGN KEY(CharityID)
 REFERENCES CMS_Charities(CharityID);

  alter table CMS_CharityPages
add foreign key(CharityID)
references CMS_Charities(CharityID);

alter table CMS_CharityPages
add foreign key (PageID) 
references CMS_Pages(PageID);

alter table CMS_CharityUsers
add foreign key(CharityID)
references CMS_Charities(CharityID);

alter table CMS_CharityUsers
add foreign key (UserID) 
references CMS_Users(UserID);

alter table CMS_CharityUsers
add foreign key (AuthLevelsID) 
references CMS_AuthLevels(AuthLevelsID);

alter table CMS_LostFounds
add foreign key (CharityID)
references CMS_Charities(CharityID);
 

alter table CMS_LostFounds
add foreign key (CreatorID) 
references CMS_Users(UserID);

alter table CMS_LostFounds
add foreign key (EditorID) 
references CMS_users(UserID);

alter table CMS_Events
add foreign key (CharityID)
references CMS_Charities(CharityID);

alter table CMS_Events
add foreign key (CreatorID) 
references CMS_Users(UserID);

alter table CMS_Events
add foreign key (EditorID) 
references CMS_users(UserID);

alter table CMS_Events
add foreign key (CharityID)
references CMS_Charities(CharityID);

alter table CMS_Events
add foreign key (CreatorID) 
references CMS_Users(UserID);

alter table CMS_Events
add foreign key (EditorID) 
references CMS_users(UserID);


alter table CMS_Pets
add foreign key (CharityID)
references CMS_Charities(CharityID);
 
alter table CMS_Pets
add foreign key (CreatorID) 
references CMS_Users(UserID);

alter table CMS_Pets
add foreign key (EditorID) 
references CMS_users(UserID);

alter table CMS_Donations
add foreign key (CharityID)
 references CMS_Charities(CharityID);

alter table CMS_Donations
add foreign key(PageID)
references CMS_Pages(PageID);
 
/*alter table CMS_Donations
add foreign key(ContentID)
references CMS_Content(ContentID);
*/

alter table CMS_Pages
add foreign key (AuthLevelsID) 
references CMS_AuthLevels(AuthLevelsID);

alter table CMS_ProposedCharities
add foreign key (CreatorID)
references CMS_Users(UserID);

alter table CMS_ProposedCharities
add foreign key (CreatorID)
references CMS_Users(UserID);

alter table CMS_Users
add foreign key (CountyID) 
references CMS_Counties(CountyID);

alter table CMS_UsersPages
add foreign key (UserID) 
references CMS_Users(UserID);

alter table CMS_UsersPages
add foreign key (PageID) 
references CMS_Pages(PageID);

alter table CMS_UsersPages
add foreign key (CharityID) 
references CMS_Charities(CharityID);

alter table CMS_Stories
add foreign key (CharityID) 
references CMS_Charities(CharityID);

alter table CMS_AccessRequest
add foreign key (UserID) 
references CMS_Users(UserID);

alter table CMS_AccessRequest
add foreign key (PageID) 
references CMS_Pages(PageID);

alter table CMS_AccessRequest
add foreign key (CharityID) 
references CMS_Charities(CharityID);

alter table CMS_Contents
add foreign key (LostFoundID)
references CMS_LostFounds(LostFoundID);

alter table CMS_Contents
add foreign key (EventID)
references CMS_Events(EventID);

alter table CMS_Contents
add foreign key (PetID)
references CMS_Pets(PetID);

alter table CMS_StoryContents
add foreign key(StoryID)
references CMS_Stories(StoryID);

alter table CMS_StoryContents
add foreign key(ContentID)
references CMS_Contents(ContentID);
