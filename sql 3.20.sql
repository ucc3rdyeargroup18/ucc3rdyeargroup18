select * from CMS_Users
where CMS_Users.UserID= ;

Select CMS_CharityUsers.UserID,CMS_CharityUsers.CharityID from CMS_CharityUsers
where CMS_CharityUsers.AuthLevelsID=3;

select CMS_AccessRequest.UserID,CMS_AccessRequest.PageID,CMS_AccessRequest.CharityID from CMS_CMS_AccessRequest
where the CMS_AccessRequest.pending = 0;







delete CMS_CharityLayout.CharityID from CMS_CharityLayout
where CMS_CharityLayout.CharityID= ;

delete CMS_CharityPages.CharityID from CMS_CharityPages
where CMS_CharityPages.CharityID=;

delete CMS_CharityUsers.CharityID from CMS_CharityUsers
where CMS_CharityUsers.CharityID=;

delete CMS_LostFounds.CharityID from CMS_LostFounds
where CMS_LostFounds.CharityID=;

delete CMS_Events.CharityID from CMS_Events
where CMS_Events.CharityID=;

delete CMS_Pets.CharityID from CMS_Pets
where CMS_Pets.CharityID=;

delete CMS_Counties.CharityID from CMS_Counties
where CMS_Counties.CharityID=;

delete CMS_Donations.CharityID from CMS_Donations
where CMS_Donations.CharityID=;

delete CMS_UsersPages.CharityID from CMS_UsersPages
where CMS_UsersPages.CharityID=;

delete CMS_Stories.CharityID from CMS_Stories
where CMS_Stories.CharityID=;

delete CMS_AccessRequest.CharityID from CMS_AccessRequest
where CMS_AccessRequest.CharityID=;


delete from CMS_Charities
where CMS_Charities.CharityID= ;


