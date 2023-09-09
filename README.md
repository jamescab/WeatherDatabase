# Weather Database

## Description:

This is a project I worked on with two other team members as a project for an Introduction to Relational Databases course. This project is for a weather database. The database records weather forecasts every hour and the average forecast within a day for a given city. This data can be used by climatologists/meteorologists to determine and analyze weather patterns in an area. Weather forecasts can then be sent to news stations to be reported. In the case of extreme weather events (hurricanes, blizzards, flash floods, etc.) advisory warnings are sent to recipients of the city with details of the event as well as instructions on how to act. The recipients that receive advisories are denoted as a Person (individual citizen; human) or as a Centre (establishments that can have lots of people that come and go from the building).

## Database details:

The database provides the following functionality:
* Add a location (city, province, population) and update the details of an entry if need be.
* Add a daily or hourly forecast.
* Select a forecast from a specific day and/or time or select a forecast spanning a range of days.
* Add an extreme weather event.
* Select events based on event type. Able to find events by aggregation.
* Add advisories for extreme weather events.
* Add recipients to be notified of any advisories.
* Can add either a centre or a person to a list of recipients.
* Able to project table of centre or person with specific attributes.
* Add a news station, update an existing news station's details or delete a news station.
* Search for news stations that broadcast for a given province.
* Search for news stations that broadcast to cities in a given province.

Documents that describe the schema and show the sample UI for the application are included in the repository.

## The ER diagram for the project can be found below:

![ER Diagram](https://github.com/jamescab/WeatherDatabase/blob/dca6883bdf718ec86809dc77dc259503fb949e5b/ERDiagram.png)


The project uses MySQL to manage and query a database that is stored into an Oracle collection. The frontend elements were made with HTML5 that would be combined with PHP to communicate between the user's queries to the backend's responses.
