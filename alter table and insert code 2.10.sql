/*CMS_CharityPageHeaders*/
alter table CMS_CharityPageHeaders
add foreign key (CharityID)
references CMS_Charities(CharityID);

alter table CMS_CharityPageHeaders
add foreign key (PageID)
references CMS_Pages(PageID);

/*CMS_CharityPages*/
alter table CMS_CharityPages
add foreign key(CharityID)
references CMS_Charities(CharityID);

alter table CMS_CharityPages
add foreign key (PageID) 
references CMS_Pages(PageID);


/*CMS_CharityUsers*/
alter table CMS_CharityUsers
add foreign key (UserID) 
references CMS_Users(UserID);

alter table CMS_CharityUsers
add foreign key (AuthLevelsID) 
references CMS_AuthLevels(AuthLevelsID);

/*CMS_Content*/
alter table CMS_Content
add foreign key (CharityID)
references CMS_Charities(CharityID);
 
alter table CMS_Content
add foreign key (PageID) 
references CMS_Pages(PageID);

alter table CMS_Content
add foreign key (CreatorID) 
references CMS_Users(UserID);

alter table CMS_Content
add foreign key (EditorID) 
references CMS_users(UserID);

/*CMS_DefaultPageHeaders*/
alter table CMS_DefaultPageHeaders
add foreign key (PageID) 
references CMS_Pages(PageID);

/*CMS_Donations*/
alter table CMS_Donations
add foreign key (CharityID)
 references CMS_Charities(CharityID);

alter table CMS_Donations
add foreign key(PageID)
references CMS_Pages(PageID);
 
alter table CMS_Donations
add foreign key(ContentID)
references CMS_Content(ContentID);

/*CMS_PageFields*/
alter table CMS_PageFields
add foreign key (PageID) 
references CMS_Pages(PageID);

/*CMS_Pages*/
alter table CMS_Pages
add foreign key (AuthLevelsID) 
references CMS_AuthLevels(AuthLevelsID);

/*CMS_ProposedCharities*/
alter table CMS_ProposedCharities
add foreign key (CreatorID)
references CMS_Users(UserID);

/*CMS_Users*/
alter table CMS_Users
add foreign key (CountyID) 
references CMS_Counties(CountyID);


/*CMS_UsersPages*/
alter table CMS_UsersPages
add foreign key (UserID) 
references CMS_Users(UserID);

alter table CMS_UsersPages
add foreign key (PageID) 
references CMS_Pages(PageID);

alter table CMS_UsersPages
add foreign key (CharityID) 
references CMS_Charities(CharityID);

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





















