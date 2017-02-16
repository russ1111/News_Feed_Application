#p3.SQL


drop table if exists Categories;
create table Categories (
        CategoryID int signed not null auto_increment primary key,
        Category varchar (120),
        Description text
);

/* Links - Technology, Climate, Space */
insert into Categories values (NULL,'Science','science links');
insert into Categories values (NULL,'World','world links');
insert into Categories values (NULL,'Health','health links');


drop table if exists Feed;
create table Feed (
        LinkID int signed not null auto_increment primary key,
        URL varchar (255),
        CategoryID int DEFAULT 0,
        Category varchar (120),
        Description text
);


/* Technology */
insert into Feed values (NULL,'https://news.google.com/news/feeds?pz=1&cf=all&ned={language}&hl={country}&q=technology&output=rss', 1,'Technology','technology links');

/* Climate */
insert into Feed values (NULL,'https://news.google.com/news/feeds?pz=1&cf=all&ned={language}&hl={country}&q=climate&output=rss', 1,'Climate','climate links');

/* Space */
insert into Feed values (NULL,'https://news.google.com/news/feeds?pz=1&cf=all&ned={language}&hl={country}&q=space&output=rss', 1,'Space','space links');

/* US */
insert into Feed values (NULL,'https://news.google.com/news/feeds?pz=1&cf=all&ned={language}&hl={country}&q=us&output=rss', 2,'US','US links');

/* EU */
insert into Feed values (NULL,'https://news.google.com/news/feeds?pz=1&cf=all&ned={language}&hl={country}&q=eu&output=rss', 2,'EU','EU links');

/* Asia */
insert into Feed values (NULL,'https://news.google.com/news/feeds?pz=1&cf=all&ned={language}&hl={country}&q=asia&output=rss', 2,'Asia','Asia links');

/* Sleep */
insert into Feed values (NULL,'https://news.google.com/news/feeds?pz=1&cf=all&ned={language}&hl={country}&q=sleep&output=rss', 3,'Sleep','sleep links');

/* Disease */
insert into Feed values (NULL,'https://news.google.com/news/feeds?pz=1&cf=all&ned={language}&hl={country}&q=disease&output=rss', 3,'Disease','disease links');

/* Medicine */
insert into Feed values (NULL,'https://news.google.com/news/feeds?pz=1&cf=all&ned={language}&hl={country}&q=medicine&output=rss', 3,'Medicine','medicine links');













