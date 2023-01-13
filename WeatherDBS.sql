create table NewsStation
	(stationID int not null,
	stationName char(20) null,
	primary key (stationID));

create table ExtremeWeatherEvent
	(dangerLevel int null,
	eventType char(10) null,
	eventID int not null,
	primary key (eventID));

create table EmployeeNorm1
	(employeeEmail char(64) null,
	employeeName char(20) not null,
	primary key (employeeEmail));

create table EmployeeNorm2
	(stationID int null,
	employeeID int not null,
	employeeEmail char(64) null,
	employeePosition char(20) null,
	primary key (employeeID),
	foreign key (stationID) references NewsStation ON DELETE CASCADE,
	foreign key (employeeEmail) references EmployeeNorm1 ON DELETE CASCADE);

create table Location
	(province char(20) not null,
	city char(20) not null,
	population int null,
	primary key (province, city));

create table Forecast
	(province char(20),
	city char(20),
	forecastID char(10),
	currDay char(10),
	currTime char(10),
	currTemp int,
	currWeather char(10),
	primary key (forecastID),
	foreign key (province, city) REFERENCES Location(province, city)
	ON DELETE CASCADE);

create table ForecastDay
	(forecastID char(10),
	day char(10),
	highTemp int,
	lowTemp int,
	dayWeather char(10),
	primary key (day, forecastID),
	foreign key (forecastID) REFERENCES Forecast
	ON DELETE CASCADE);

create table ForecastHour
	(forecastID char(10) NOT NULL,
	day char(10),
	time char(10),
	temp int,
	hourWeather char(10),
	primary key (day, time, forecastID),
	foreign key (forecastID) REFERENCES Forecast(forecastID)
	ON DELETE CASCADE);

create table Advisory
	(eventID int not null,
	advisoryType char(30) null,
	advisoryMessage char(64) null,
	advisoryID int not null,
	primary key (advisoryID),
	foreign key (eventID) references ExtremeWeatherEvent(eventID) ON DELETE CASCADE);

create table Recipient
	(recipientID int not null,
	phoneNum int null,
	primary key (recipientID));

create table CentreNorm2
	(centreID int,
	centreName char(30),
	centrePopulation int,
	centreAddress char(30),
	primary key (centreID));


create table CentreNorm1
	(recipientID int,
	centreID int,
	primary key (recipientID),
	foreign key (centreID) references CentreNorm2(centreID),
	foreign key (recipientID) references Recipient(recipientID));

create table PersonNorm2
        (sin int,
        personName char(30),
        address char(30),
        primary key (sin));

create table PersonNorm1
	(recipientID int,
	sin int,
	primary key (recipientID),
	foreign key (recipientID) references Recipient(recipientID),
	foreign key (sin) references PersonNorm2(sin));

create table SentTo
	(advisoryID int,
	recipientID int,
	primary key (advisoryID, recipientID),
	foreign key (advisoryID) references Advisory(advisoryID),
	foreign key (recipientID) references Recipient(recipientID));

create table BroadcastsTo
	(stationID int,
	province char(20),
	city char(20),
	primary key (stationID, province, city),
	foreign key (stationID) references NewsStation ON DELETE CASCADE,
	foreign key (province, city) references Location(province, city));

create table Receives
	(stationID int,
	forecastID char(10),
	primary key (stationID, forecastID),
	foreign key (stationID) references NewsStation(stationID) ON DELETE CASCADE,
	foreign key (forecastID) references Forecast(forecastID) ON DELETE CASCADE);

create table Reports
	(forecastID char(10),
	eventID int,
	primary key (forecastID, eventID),
	foreign key (forecastID) references Forecast(forecastID),
	foreign key (eventID) references ExtremeWeatherEvent(eventID));



insert into NewsStation(stationID, stationName) values (01, 'Main Ontario');
insert into NewsStation(stationID, stationName) values (10, 'CNV');
insert into NewsStation(stationID, stationName) values (22, 'TBS');
insert into NewsStation(stationID, stationName) values (99, 'Main Toronto');
insert into NewsStation(stationID, stationName) values (34, 'Sub Kelowna');
insert into NewsStation(stationID, stationName) values (02, 'Global Vancouver');
insert into NewsStation(stationID, stationName) values (03, 'CTV Vancouver');
insert into NewsStation(stationID, stationName) values (04, 'CTV Victoria');
insert into NewsStation(stationID, stationName) values (05, 'King News');
insert into NewsStation(stationID, stationName) values (06, 'Global BC');
insert into NewsStation(stationID, stationName) values (07, 'Global National');
insert into NewsStation(stationID, stationName) values (08, 'Global Edmonton');
insert into NewsStation(stationID, stationName) values (09, 'Calgary News');
insert into NewsStation(stationID, stationName) values (11, 'Edmonton News');
insert into NewsStation(stationID, stationName) values (12, 'Global Alberta');
insert into NewsStation(stationID, stationName) values (13, 'LCN Quebec');
insert into NewsStation(stationID, stationName) values (14, 'Montreal Gazette');
insert into NewsStation(stationID, stationName) values (15, 'Ottawa News');

insert into ExtremeWeatherEvent (dangerLevel, eventType, eventID) values (8, 'Hurricane', 23114);
insert into ExtremeWeatherEvent (dangerLevel, eventType, eventID) values (2, 'Heatwave', 22432);
insert into ExtremeWeatherEvent (dangerLevel, eventType, eventID) values (4, 'Blizzard', 43123);
insert into ExtremeWeatherEvent (dangerLevel, eventType, eventID) values (6, 'Tornado', 11345);
insert into ExtremeWeatherEvent (dangerLevel, eventType, eventID) values (5, 'Flood', 61523);
insert into ExtremeWeatherEvent (dangerLevel, eventType, eventID) values (1, 'Heavy Rain', 90129);
insert into ExtremeWeatherEvent (dangerLevel, eventType, eventID) values (5, 'Forest Fire', 81230);
insert into ExtremeWeatherEvent (dangerLevel, eventType, eventID) values (9, 'Earthquake', 90210);
insert into ExtremeWeatherEvent (dangerLevel, eventType, eventID) values (3, 'Blizzard', 22113);
insert into ExtremeWeatherEvent (dangerLevel, eventType, eventID) values (1, 'High Winds', 31249);

insert into EmployeeNorm1 (employeeName, employeeEmail) values ('John Reed', 'jr045@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Sara Howard', 'sarahow@gmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Antonio Brown', 'AB84@outlook.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Samantha Anderson', 'samanthand143@gmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Walter Payton', 'paytonw34@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Bobby Joe', 'bobbyjoe@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Jenny Lee', 'jennylee@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Freddy Bob', 'freddybob@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Larry King', 'larryking@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Tamerra Tag', 'tt@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Eric Lowe', 'el@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Nicole Cheung', 'nc@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Molly Tang', 'mt@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Karen Brown', 'kb@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Sarah Song', 'ss@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Jared Joe', 'jj@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Jacob Smith', 'js@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Sam Irwin', 'si@hotmail.com');
insert into EmployeeNorm1 (employeeName, employeeEmail) values ('Flo Jackson', 'fj@hotmail.com');

insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (01, 045, 'jr045@hotmail.com', 'producer');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (10, 023, 'sarahow@gmail.com', 'anchor reporter');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (22, 084, 'AB84@outlook.com', 'anchor reporter');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (99, 143, 'samanthand143@gmail.com', 'field reporter');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (34, 034, 'paytonw34@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (01, 001, 'bobbyjoe@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (02, 002, 'jennylee@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (03, 003, 'freddybob@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (04, 004, 'larryking@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (05, 005, 'tt@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (06, 006, 'el@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (07, 007, 'nc@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (08, 008, 'mt@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (09, 009, 'kb@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (11, 011, 'ss@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (12, 012, 'jj@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (13, 013, 'js@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (14, 014, 'si@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (15, 015, 'tt@hotmail.com', 'meteorologist');
insert into EmployeeNorm2 (stationID, employeeID, employeeEmail, employeePosition) values (15, 016, 'fj@hotmail.com', 'meteorologist');

insert into Location (province, city, population) values ('British Columbia', 'Vancouver', 675218 );
insert into Location (province, city, population) values ('British Columbia', 'Victoria', 92141 );
insert into Location (province, city, population) values ('British Columbia', 'Kelowna', 132084 );
insert into Location (province, city, population) values ('Ontario', 'Toronto', 2930000);
insert into Location (province, city, population) values ('Alberta', 'Calgary', 1336000);
insert into Location (province, city, population) values ('Alberta', 'Edmonton', 981280);
insert into Location (province, city, population) values ('Quebec', 'Montreal', 1780000);
insert into Location (province, city, population) values ('Quebec ', 'Quebec City', 542298);
insert into Location (province, city, population) values ('Ontario', 'Ottawa', 994837);

insert into Forecast(province, city, forecastID, currDay, currTime, currTemp, currWeather) values ('British Columbia', 'Vancouver', 'bc00010001', '2019-10-29', '12:00:00', 20, 'Cloudy');
insert into Forecast(province, city, forecastID, currDay, currTime, currTemp, currWeather) values ('British Columbia', 'Vancouver', 'bc00012345', '2019-11-11', '12:00:00', 11, 'Raining');
insert into Forecast(province, city, forecastID, currDay, currTime, currTemp, currWeather) values ('Ontario', 'Toronto', 'on20347802', '2019-12-09', '12:00:00', 5, 'Cloudy');
insert into Forecast(province, city, forecastID, currDay, currTime, currTemp, currWeather) values ('Quebec', 'Montreal', 'qb00012345', '2020-02-22', '12:00:00', -2, 'Snowing');
insert into Forecast(province, city, forecastID, currDay, currTime, currTemp, currWeather) values ('Ontario', 'Ottawa', 'on00201378', '2020-06-20', '12:00:00', 24, 'Sunny');

insert into ForecastDay(forecastID, day, highTemp, lowTemp, dayWeather) values ('bc00010001', '2019-10-29', 23, 18, 'Cloudy');
insert into ForecastDay(forecastID, day, highTemp, lowTemp, dayWeather) values ('bc00012345', '2019-11-11', 10, 12, 'Raining');
insert into ForecastDay(forecastID, day, highTemp, lowTemp, dayWeather) values ('on20347802', '2019-12-09', 3, 7, 'Cloudy');
insert into ForecastDay(forecastID, day, highTemp, lowTemp, dayWeather) values ('qb00012345', '2020-02-22', -5, 3, 'Snowing');
insert into ForecastDay(forecastID, day, highTemp, lowTemp, dayWeather) values ('on00201378', '2020-06-05', 28, 20, 'Sunny');

insert into ForecastHour(forecastID, day, time, temp, hourWeather) values ('bc00010001', '2019-10-29', '13:05:22', 23, 'Cloudy');
insert into ForecastHour(forecastID, day, time, temp, hourWeather) values ('bc00012345', '2019-11-11', '17:24:55', 10, 'Foggy');
insert into ForecastHour(forecastID, day, time, temp, hourWeather) values ('on20347802', '2019-12-09', '09:08:12', 5, 'Cloudy');
insert into ForecastHour(forecastID, day, time, temp, hourWeather) values ('qb00012345', '2020-02-22', '06:56:07', -5, 'Frost');
insert into ForecastHour(forecastID, day, time, temp, hourWeather) values ('on00201378', '2020-06-05', '14:25:44', 27, 'Sunny');

insert into Advisory (eventID, advisoryType, advisoryMessage, advisoryID) values (23114, 'EVACUATE', 'HURRICANE WARNING: EVACUATE OUT OF THE CITY', 02);
insert into Advisory (eventID, advisoryType, advisoryMessage, advisoryID) values (22432, 'HEAT WARNING', 'HOT WEATHER: REMAIN COOL AND WEAR SUN PROTECTION', 01);
insert into Advisory (eventID, advisoryType, advisoryMessage, advisoryID) values (43123, 'SHELTER', 'BLIZZARD WARNING: SEEK SHELTER AND STAY INDOORS', 03);
insert into Advisory (eventID, advisoryType, advisoryMessage, advisoryID) values (11345, 'SEEK REFUGE', 'TORNADO WARNING: SEEK REFUGE IN CELLAR OR OUT OF TORNADO ZONE', 05);
insert into Advisory (eventID, advisoryType, advisoryMessage, advisoryID) values (61523, 'EVACUATE', 'FLOOD WARNING: HEAD TO HIGHER ELEVATION OR OUT OF AFFECTED AREAS', 04);
insert into Advisory (eventID, advisoryType, advisoryMessage, advisoryID) values (90129, 'TAKE CARE', 'HEAVY RAIN: BRING AN UMBRELLA', 06);
insert into Advisory (eventID, advisoryType, advisoryMessage, advisoryID) values (81230, 'AIR CAUTION', 'FOREST FIRE: AVOID TIME SPENT IN OPEN AIR', 07);
insert into Advisory (eventID, advisoryType, advisoryMessage, advisoryID) values (90210, 'QUAKE WARNING', 'QUAKE WARNING: HIDE UNDER STABLE STRUCTURE', 08);
insert into Advisory (eventID, advisoryType, advisoryMessage, advisoryID) values (22113, 'SHELTER', 'BLIZZARD WARNING: SEEK SHELTER AND STAY INDOORS', 09);
insert into Advisory (eventID, advisoryType, advisoryMessage, advisoryID) values (31249, 'WIND CAUTION', 'HIGH WINDS: TAKE CARE OF HIGH WINDS BLOWING IN', 10);

insert into Recipient (recipientID, phoneNum) values (24218, 3073065581);
insert into Recipient (recipientID, phoneNum) values (47949, 7283519564);
insert into Recipient (recipientID, phoneNum) values (61948, 8496307011);
insert into Recipient (recipientID, phoneNum) values (81841, 8066439719);
insert into Recipient (recipientID, phoneNum) values (65695, 3424529319);
insert into Recipient (recipientID, phoneNum) values (28214, 4009002000);
insert into Recipient (recipientID, phoneNum) values (99447, 2008001000);
insert into Recipient (recipientID, phoneNum) values (98614, 7001239000);
insert into Recipient (recipientID, phoneNum) values (88411, 806406306);
insert into Recipient (recipientID, phoneNum) values (96655, 3423423424);
insert into Recipient (recipientID, phoneNum) values (12345, 6049899999);
insert into Recipient (recipientID, phoneNum) values (12346, 7789989998);
insert into Recipient (recipientID, phoneNum) values (12347, 7786479382);
insert into Recipient (recipientID, phoneNum) values (12348, 6048793945);
insert into Recipient (recipientID, phoneNum) values (12349, 6049879293);
insert into Recipient (recipientID, phoneNum) values (12356, 6046046044);
insert into Recipient (recipientID, phoneNum) values (12357, 7788871000);
insert into Recipient (recipientID, phoneNum) values (12359, 2500101010);
insert into Recipient (recipientID, phoneNum) values (12363, 6048059999);
insert into Recipient (recipientID, phoneNum) values (12379, 7649120928);

insert into CentreNorm2 (centreID, centreName, centrePopulation, centreAddress) values (11, 'UBC Hospital', 5000, '2211 Wesbrook Mall');
insert into CentreNorm2 (centreID, centreName, centrePopulation, centreAddress) values (12, 'Lord Byng Secondary School', 2000, '3939 W 16th Ave');
insert into CentreNorm2 (centreID, centreName, centrePopulation, centreAddress) values (13, 'Vancouver Aquarium', 800, '845 Avison Way');
insert into CentreNorm2 (centreID, centreName, centrePopulation, centreAddress) values (14, 'Science World', 1000, '1455 Quebec St');
insert into CentreNorm2 (centreID, centreName, centrePopulation, centreAddress) values (15, 'Lord Roberts Elementary School', 500, '1100 Bidwell St');
insert into CentreNorm2 (centreID, centreName, centrePopulation, centreAddress) values (16, 'Art Gallery', 200, '750 Hornby St');
insert into CentreNorm2 (centreID, centreName, centrePopulation, centreAddress) values (17, 'YVR', 1000, '3211 Grant McConachie Way');
insert into CentreNorm2 (centreID, centreName, centrePopulation, centreAddress) values (18, 'Playland', 2500, '2901 E Hastings St');
insert into CentreNorm2 (centreID, centreName, centrePopulation, centreAddress) values (19, 'Rogers Arena', 3500, '800 Griffiths Way');
insert into CentreNorm2 (centreID, centreName, centrePopulation, centreAddress) values (20, 'Vancouver Library', 900, '350 W Georgia St');

insert into CentreNorm1 (recipientID, centreID) values (24218, 11);
insert into CentreNorm1 (recipientID, centreID) values (47949, 12);
insert into CentreNorm1 (recipientID, centreID) values (61948, 13);
insert into CentreNorm1 (recipientID, centreID) values (81841, 14);
insert into CentreNorm1 (recipientID, centreID) values (65695, 15);
insert into CentreNorm1 (recipientID, centreID) values (28214, 16);
insert into CentreNorm1 (recipientID, centreID) values (99447, 17);
insert into CentreNorm1 (recipientID, centreID) values (98614, 18);
insert into CentreNorm1 (recipientID, centreID) values (88411, 19);
insert into CentreNorm1 (recipientID, centreID) values (96655, 20);

insert into PersonNorm2 (personName, sin, address) values ('Nicole Cheung', 111111111, '1234 West 2nd Ave');
insert into PersonNorm2 (personName, sin, address) values ('Clara Park', 222222222, '5678 West 3nd Ave');
insert into PersonNorm2 (personName, sin, address) values ('James Cabaral', 333333333, '9090 West 4nd Ave');
insert into PersonNorm2 (personName, sin, address) values ('Joe Joe', 444444444, '1000 West 5th Ave');
insert into PersonNorm2 (personName, sin, address) values ('Bobby Bob', 555555555, '8888 West 6th Ave');
insert into PersonNorm2 (personName, sin, address) values ('Thomas Cruz', 666666666, '4321 West 2nd Ave');
insert into PersonNorm2 (personName, sin, address) values ('Anjelica Jolie', 777777777, '5251 West 3nd Ave');
insert into PersonNorm2 (personName, sin, address) values ('Bradley Pitts', 888888888, '');
insert into PersonNorm2 (personName, sin, address) values ('Clinton Westwood', 999999999, '4500 West 5th Ave');
insert into PersonNorm2 (personName, sin, address) values ('Christian Bailey', 123456789, '3246 West 6th Ave');

insert into PersonNorm1 (recipientID, sin) values (12345, 111111111);
insert into PersonNorm1 (recipientID, sin) values (12346, 222222222);
insert into PersonNorm1 (recipientID, sin) values (12347, 333333333);
insert into PersonNorm1 (recipientID, sin) values (12348, 444444444);
insert into PersonNorm1 (recipientID, sin) values (12349, 555555555);
insert into PersonNorm1 (recipientID, sin) values (12356, 666666666);
insert into PersonNorm1 (recipientID, sin) values (12357, 777777777);
insert into PersonNorm1 (recipientID, sin) values (12359, 888888888);
insert into PersonNorm1 (recipientID, sin) values (12363, 999999999);
insert into PersonNorm1 (recipientID, sin) values (12379, 123456789);

insert into SentTo (advisoryID, recipientID) values (01, 24218);
insert into SentTo (advisoryID, recipientID) values (02, 47949);
insert into SentTo (advisoryID, recipientID) values (03, 61948);
insert into SentTo (advisoryID, recipientID) values (04, 81841);
insert into SentTo (advisoryID, recipientID) values (05, 65695);

insert into BroadcastsTo(stationID, province, city) values (01, 'Ontario', 'Ottawa');
insert into BroadcastsTo(stationID, province, city) values (01, 'Ontario', 'Toronto');
insert into BroadcastsTo(stationID, province, city) values (10, 'British Columbia', 'Vancouver');
insert into BroadcastsTo(stationID, province, city) values (22, 'British Columbia', 'Vancouver');
insert into BroadcastsTo(stationID, province, city) values (99, 'Ontario', 'Toronto');
insert into BroadcastsTo(stationID, province, city) values (34, 'British Columbia', 'Kelowna');
insert into BroadcastsTo(stationID, province, city) values (02, 'British Columbia', 'Vancouver');
insert into BroadcastsTo(stationID, province, city) values (03, 'British Columbia', 'Vancouver');
insert into BroadcastsTo(stationID, province, city) values (04, 'British Columbia', 'Victoria');
insert into BroadcastsTo(stationID, province, city) values (05, 'British Columbia', 'Victoria');
insert into BroadcastsTo(stationID, province, city) values (05, 'British Columbia', 'Vancouver');
insert into BroadcastsTo(stationID, province, city) values (05, 'British Columbia', 'Kelowna');
insert into BroadcastsTo(stationID, province, city) values (06, 'British Columbia', 'Victoria');
insert into BroadcastsTo(stationID, province, city) values (06, 'British Columbia', 'Vancouver');
insert into BroadcastsTo(stationID, province, city) values (06, 'British Columbia', 'Kelowna');
insert into BroadcastsTo(stationID, province, city) values (07, 'British Columbia', 'Victoria');
insert into BroadcastsTo(stationID, province, city) values (07, 'British Columbia', 'Vancouver');
insert into BroadcastsTo(stationID, province, city) values (07, 'British Columbia', 'Kelowna');
insert into BroadcastsTo(stationID, province, city) values (07, 'Ontario', 'Toronto');
insert into BroadcastsTo(stationID, province, city) values (07, 'Alberta', 'Calgary');
insert into BroadcastsTo(stationID, province, city) values (07, 'Alberta', 'Edmonton');
insert into BroadcastsTo(stationID, province, city) values (07, 'Quebec', 'Montreal');
insert into BroadcastsTo(stationID, province, city) values (07, 'Quebec ', 'Quebec City');
insert into BroadcastsTo(stationID, province, city) values (07, 'Ontario', 'Ottawa');
insert into BroadcastsTo(stationID, province, city) values (08, 'Alberta', 'Edmonton');
insert into BroadcastsTo(stationID, province, city) values (09, 'Alberta', 'Calgary');
insert into BroadcastsTo(stationID, province, city) values (11, 'Alberta', 'Edmonton');
insert into BroadcastsTo(stationID, province, city) values (12, 'Alberta', 'Calgary');
insert into BroadcastsTo(stationID, province, city) values (12, 'Alberta', 'Edmonton');
insert into BroadcastsTo(stationID, province, city) values (13, 'Quebec', 'Montreal');
insert into BroadcastsTo(stationID, province, city) values (13, 'Quebec ', 'Quebec City');
insert into BroadcastsTo(stationID, province, city) values (14, 'Quebec', 'Montreal');
insert into BroadcastsTo(stationID, province, city) values (15, 'Ontario', 'Ottawa');

insert into Receives(stationID, forecastID) values (22, 'bc00012345');
insert into Receives(stationID, forecastID) values (01, 'bc00010001');
insert into Receives(stationID, forecastID) values (99, 'bc00012345');
insert into Receives(stationID, forecastID) values (34, 'on00201378');
insert into Receives(stationID, forecastID) values (10, 'bc00010001');

insert into Reports(forecastID, eventID) values ('bc00010001', 23114);
insert into Reports(forecastID, eventID) values ('bc00012345', 22432);
insert into Reports(forecastID, eventID) values ('on20347802', 43123);
insert into Reports(forecastID, eventID) values ('qb00012345', 11345);
insert into Reports(forecastID, eventID) values ('on00201378', 61523);
