Create table CMS_AuthLevels(
 AuthLevelsID int NOT NULL,
 Level1 VarChar(16) NOT NULL,
 primary key (AuthLevelsID)
 );

 Create table CMS_Charities(
 CharityID int NOT NULL,
 Name varchar(32) NOT NULL,
 CountyID int NOT NULL,
 Address1 varchar(32) NOT NULL,
 Address2 varchar(32) NOT NULL,
 Phone varchar(16) NOT NULL,
 DomainName varchar(32) NOT NULL,
 CharityNo varchar(10) NOT NULL,
 BIC varchar(8) NOT NULL,
 IBAN varchar(20)NOT NULL,
 primary key (CharityID),
foreign key (CountyID) references CMS_Counties(CountyID)
 );
 
  Create table CMS_CharityLayout(
 CharityLayoutID int NOT NULL,
 CharityID int NOT NULL,
 Color1 Char(6)NOT NULL,
 Color2 Char(6)NOT NULL,
 Color3 Char(6)NOT NULL,
 Logo Nvarchar(255)NOT NULL,
 primary key (CharityLayoutID),
 foreign key (CharityID) references  CMS_Charities(CharityID)
 );

 
 Create table CMS_CharityPageHeaders
 (
 CharityPageHeadersID int NOT NULL,
 CharityID int  NOT NULL,
 PageID int NOT NULL,
 Header1 varchar(32)NOT NULL,
 Header2 varchar(32),
 Header3 varchar(32),
 Header4 varchar(32),
 Header5 varchar(32),
 Header6 varchar(32),
 Header7 varchar(32),
 Header8 varchar(32),
 Header9 varchar(32),
 Header10 varchar(32),
 Header11 varchar(32),
 Header12 varchar(32),
 Header13 varchar(32),
 Header14 varchar(32),
 primary key (CharityPageHeadersID),
 foreign key (CharityID) references CMS_Charities(CharityID),
 foreign key (PageID) references CMS_Pages(PageID)
 );

 Create table CMS_CharityPages
 (
 CharityPageID int NOT NULL,
 CharityID int NOT NULL,
 PageID int NOT NULL,
 CustomTitle varchar(32)NOT NULL,
 primary key (CharityPageID),
 foreign key (CharityID) references CMS_Charities(CharityID),
 foreign key (PageID) references CMS_Pages(PageID)
 );

  Create table CMS_CharityUsers(
 CharityUsersID int NOT NULL,
 CharityID int NOT NULL,
 UserID int NOT NULL,
 AuthLevelsID int NOT NULL,
 primary key (CharityUsersID),
 foreign key (CharityID) references CMS_Charities(CharityID),
 foreign key (UserID) references CMS_Users(UserID),
 foreign key (AuthLevelsID) references CMS_AuthLevels(AuthLevelsID)
);

Create table CMS_Content(
ContentID int NOT NULL,
CharityID int NOT NULL,
PageID int NOT NULL,
CreatorID int NOT NULL,
CreatedOn DateTime NOT NULL,
EditorID int,
EditedOn DateTime,
Title varchar(32) NOT NULL,
SubTitle varchar(32),
Text1 Text,
Text2 Text,
Nvarchar1 varchar(255),
Nvarchar2 varchar(255),
DateTime1 DateTime,
DateTime3 DateTime,
Int11 int,
Int22 int,
Float1 float,
Float2 float,
Image1 blob,
Image2 blob,
Bool1 tinyint,
Bool2 tinyint,
primary key (ContentID) ,
foreign key (CharityID) references CMS_Charities(CharityID),
foreign key (PageID) references CMS_Pages(PageID),
foreign key (CreatorID) references CMS_Users(UserID),
foreign key (EditorID) references CMS_users(UserID)

);

Create table CMS_Counties(
CountyID int NOT NULL,
County varchar(16) NOT NULL,
primary key (CountyID)
);

Create table CMS_DefaultPageHeaders(
DefaultPageHeadersID int NOT NULL,
PageID int NOT NULL,
Header1 varchar(32) NOT NULL,
Header2 varchar(32),
Header3 varchar(32),
Header4 varchar(32),
Header5 varchar(32),
Header6 varchar(32),
Header7 varchar(32),
Header8 varchar(32),
Header9 varchar(32),
Header10 varchar(32),
Header11 varchar(32),
Header12 varchar(32),
Header13 varchar(32),
Header14 varchar(32),
primary key (DefaultPageHeadersID),
foreign key (PageID) references CMS_Pages(PageID)
);

Create table CMS_Donations(
DonationID int NOT NULL,
CharityID int NOT NULL,
Message varchar(255) NOT NULL,
Amount Decimal(6,2) NOT NULL,
Timestamp1 DateTime NOT NULL,
PageID int NOT NULL,
ContentID int NOT NULL,
primary key (DonationID),
foreign key (CharityID) references CMS_Charities(CharityID),
foreign key(PageID) references CMS_Pages(PageID),
foreign key(ContentID) references CMS_Content(ContentID)
);

Create table CMS_PageFields(
PageFieldsID int NOT NULL,
PageID int NOT NULL,
Text1 tinyint NOT NULL,
Text2 tinyint NOT NULL,
Nvarchar1 tinyint NOT NULL,
Nvarchar2 tinyint NOT NULL,
DateTime1 tinyint NOT NULL,
DateTime3 tinyint NOT NULL,
Int11 tinyint NOT NULL,
Int22 tinyint NOT NULL,
Float1 tinyint NOT NULL,
Float2 tinyint NOT NULL,
Image1 tinyint NOT NULL,
Image2 tinyint NOT NULL,
Bool1 tinyint NOT NULL,
Bool2 tinyint NOT NULL,
primary key (PageFieldsID),
foreign key (PageID) references CMS_Pages(PageID)

);

Create table CMS_Pages(
PageID int NOT NULL,
Name varchar(32) NOT NULL,
FileName varchar(32) NOT NULL,
AuthLevelsID int NOT NULL,
primary key (PageID),
foreign key (AuthLevelsID) references CMS_AuthLevels(AuthLevelsID)
);

Create table CMS_ProposedCharities(
ProposedCharitiesID int NOT NULL,
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
primary key (ProposedCharitiesID),
foreign key (CreatorID) references CMS_Users(UserID)
);

Create table CMS_Users(
UserID int NOT NULL,
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
primary key (UserID),
foreign key (CountyID) references CMS_Counties(CountyID)

);

Create table CMS_UsersPages(
UserPageID int NOT NULL,
UserID int NOT NULL,
PageID int NOT NULL,
CharityID int NOT NULL,
primary key (UserPageID),
foreign key (UserID) references CMS_Users(UserID),
foreign key (PageID) references CMS_Pages(PageID),
foreign key (CharityID) references CMS_Charities(CharityID)
);
