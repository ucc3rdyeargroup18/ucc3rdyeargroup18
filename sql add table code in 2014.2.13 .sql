Create table CMS_Stories(
StoryID int NOT NULL,
title varchar(32) NOT NULL,
CharityID INT NOT NULL ,
primary key (StoryID),
foreign key (CharityID) references CMS_Charities(CharityID)
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

ALTER TABLE CMS_Content
ADD column_name StoryID;

ALTER TABLE CMS_Content
ADD FOREIGN KEY (StoryID)
REFERENCES CMS_Stories(StoryID);
