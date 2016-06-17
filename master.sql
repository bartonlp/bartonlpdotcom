
use barton;

drop table if exists master;

create table master (
  id int(11) auto_increment,
  site varchar(30),
  page varchar(255),
  ip varchar(50),
  agent varchar(255),
  finger varchar(40) default null,
  count int(11) default 0,
  robot int(6) default 0,
  which int(6) default 0,
  tracker int(6) default 0,
  memberId int(11) default 0,
  visits int(11) default 0,
  created datetime default null,
  starttime datetime default null,
  endtime datetime default null,
  lasttime timestamp default current_timestamp,
  primary key(id, site, ip, agent, finger)
)  default charset=utf8;

