# Liturgical Calendar
A PHP script that will generate the liturgical calendar for any given year, based on the General Roman Calendar, calculating the mobile festivities and the precedence of solemnities, feasts, memorials... This script serves as a data endpoint, which will generate the data for the General Roman Calendar in a data exchange format, such as JSON, XML, or ICS. An example of the endpoint can be found at https://johnromanodorazio.com/LiturgicalCalendar/, at the first link on the page [*data generation endpoint here*](https://johnromanodorazio.com/LiturgicalCalendar/LitCalEngine.php).

Some characteristics of this endpoint:
* **The data is based on official sources**, not copied from random internet sources. Sources used are the various editions of the **Roman Missal** in Latin, English, and Italian, **Magisterial documents**, and the **Decrees of the Congregation for Divine Worship**
    - Missale Romanum, Editio typica, 1970
    - Missale Romanum, Reimpressio emendata, 1971
    - Missale Romanum, Editio typica secunda, 1975
    - Missale Romanum, Editio typica tertia, 2002
    - Missale Romanum, Editio typica tertia emendata, 2008
    - [Mysterii Paschalis, PAULUS PP. VI, 1969](http://www.vatican.va/content/paul-vi/la/motu_proprio/documents/hf_p-vi_motu-proprio_19690214_mysterii-paschalis.html)
    - [Decrees of the Congregation of Divine Worship](https://www.vatican.va/roman_curia/congregations/ccdds/index_it.htm)
* **The data is historically accurate**, *i.e.* the liturgical calendar produced for the year 1979 will reflect the calendar as it was in that year, and not as it would be today (obviously future years will reflect the calendar as it is generated in the current year; as new decrees are issued by the Congregation for Divine Worship or new editions of the Roman Missal are published, the script will need to be updated to account for any new criteria)


# How to setup the endpoint yourself

### 1. Setup the database tables
  Included is an SQL dump **`liturgy.sql`** which contains database tables with fixed date festivities, memorials and optional memorials of the General Roman Calendar. The PHP script aids itself with these tables to generate the liturgical calendar along with the localized translations of the festivities. The tables themselves do not necessarily define the dates of all festivities, for example movable feasts are necessarily calculated in the script itself, in this case the tables serve only for localization purposes.
### 2. Edit the configuration file for access to the database
  Once the tables are set up in a database, the `LitCalConfig-sample.php` file must be renamed to `LitCalConfig.php` and populated with the credentials to access the database. Seeing that this file will be renamed for production, it will not be overwritten when the scripts get updated from a new release or a `git pull`.

# How to use the endpoint
There are a few proof of concept example applications for usage of the endpoint at https://johnromanodorazio.com/LiturgicalCalendar/, which demonstrate generating an HTML representation of the Liturgical Calendar.

* The [first example](https://www.johnromanodorazio.com/LiturgicalCalendar/examples/php/) uses cURL in PHP to make a request to the endpoint and handle the results. 
* The [second example](https://www.johnromanodorazio.com/LiturgicalCalendar/examples/javascript/) uses AJAX in Javascript to make the request to the endpoint and handle the results.
* The [third example](https://www.johnromanodorazio.com/LiturgicalCalendar/examples/fullcalendar/examples/month-view.html) makes use of the [FullCalendar javascript framework](https://github.com/fullcalendar/fullcalendar) to display the results from the AJAX request in a nicely formatted calendar view.
* The [fourth example](https://www.johnromanodorazio.com/LiturgicalCalendar/examples/fullcalendar/examples/messages.html) is the same as the third except that it outputs the Messages first and the [FullCalendar](https://github.com/fullcalendar/fullcalendar) calendar view after.

All of these examples request `JSON` as the data exchange format generated by the endpoint. Any application could use the endpoint in a similar manner: an Android App, a plugin for a Desktop Publishing App...

## Parameters that can be used in the request to the endpoint
* ***`locale`***: can have a value of *`EN`*, *`IT`*, or *`LA`* (*default*: ***`LA`***). Sets the desired localization for the Calendar to *English*, *Italian*, or *Latin* respectively.
* ***`epiphany`***: can have a value of *`SUNDAY_JAN2_JAN8`* or *`JAN6`* (*default*: ***`JAN6`***). Indicates whether Epiphany should fall exactly on January 6th or instead on the Sunday between January 2nd and January 8th. Traditionally it falls on January 6th in the Vatican, but each regional Conference of Bishops can opt to celebrate on the Sunday.
* ***`ascension`***: can have a value of *`THURSDAY`* or *`SUNDAY`* (*default*: ***`SUNDAY`***). Indicates whether the feast of the Ascension should fall on a Thursday or on a Sunday. Traditionally in the Vatican it falls on a Thursday, but for pastoral reasons each regional Conference of Bishops can opt to celebrate on Sunday.
* ***`corpuschristi`***: can have a value of *`THURSDAY`* or *`SUNDAY`* (*default*: ***`SUNDAY`***). Indicates whether the feast of Corpus Christi should fall on a Thursday or on a Sunday. Traditionally in the Vatican it falls on a Thursday, but for pastoral reasons each regional Conference of Bishops can opt to celebrate on Sunday.
* ***`nationalpreset`***: supported values as of v2.8 are *`VATICAN`*, *`ITALY`*, and *`USA`*. This will take precedence over the previous parameters. For example, a value of `ITALY` will automatically set `locale` to `IT`, `epiphany` to `JAN6`, `ascension` to `SUNDAY` and `corpuschristi` to `SUNDAY`, and it will add to / modify the calendar with those celebrations that are proper to the Italian Missal. Similarly a value of `USA` will automatically set `locale` to `EN`, `epiphany` to `SUNDAY_JAN2_JAN8`, `ascension` to `SUNDAY` and `corpuschristi` to `SUNDAY`, and will add to / modify the calendar with the celebrations that are proper to the English USA Missal. A value of `VATICAN` will set `locale` to `LA`, `epiphany` to `JAN6`, `ascension` to `THURSDAY` and `corpuschristi` to `THURSDAY` (even though these last two might change from to year within the Vatican).
* ***`diocesanpreset`***: only supported value as of v2.8 is *`DIOCESIDIROMA`*, (a value of `DIOCESILAZIO` is equivalent to `DIOCESIDIROMA` as of v2.8). This will take precedence over the previous parameters. For example, a value of `DIOCESIDIROMA` will automatically set `nationalpreset` to `ITALY`, and will add to / modify the calendar with those celebrations that are proper to the Diocese of Rome, based on the calendar for Italy.
* ***`year`***: can have a value starting from *`1970`* and a maximum value of *`9999`* (*default*: ***current year***). For the time being, this endpoint only calculates the Liturgical Calendar that follows the reform of the Second Vatican Council, starting from the publishing of the *Editio Typica* of the Roman Missal in 1970. Perhaps in future updates information from the calendar preceding the Second Vatican Council will be added, in order to have a greater historical range from the endpoint.
* ***`returntype`***: can have a value of *`JSON`*, *`XML`*, or *`ICS`* (*default*: ***`JSON`***). Indicates the format of the data that will be returned by the endpoint. **N.B.** the desired data type should also be detected from the `Accept header` set by the requesting client, if not indicated by means of the `returntype` parameter; in this case, possible values are `application/json`, `application/xml`, and `text/calendar`

**N.B.** The parameter names are expected to be in lowercase characters. The parameter values are generally expected to be in uppercase characters, but they will work in lowercase characters just as well (starting from v2.5).

A sample request to the endpoint could look like this:

https://johnromanodorazio.com/LiturgicalCalendar/LitCalEngine.php?year=2020&epiphany=SUNDAY_JAN2_JAN8&ascension=SUNDAY&corpuschristi=SUNDAY&returntype=JSON&locale=EN

If no parameters are given, the default values indicated above will be used.

Both **POST** and **GET** requests can be made.

## Using the endpoint as a calendar URL for Calendar Apps
* **GOOGLE CALENDAR ON A DESKTOP COMPUTER**: you can only *add a calendar by URL* using Google Calendar on a computer, I don't believe it is possible from smartphone / Android devices. At the bottom left corner of the screen, next to **`Other calendars`**, click on the **`+`** to add a new calendar and choose **`From URL`**. Paste in the URL of the endpoint with the desired parameters, (make sure you use **`ICS`** as value of the *`returntype`* parameter). And remember, if you omit the *`year`* parameter, it will use the current year. This should mean that as Google Calendar continues to poll the calendar URL (supposedly every 8 hours), on the turn of a new year new events should be created automatically for the new year. Once the calendar has been added from a computer, it should become available for the same gmail account on the Google Calendar app on a smartphone.
* **CALENDAR APPS ON AN ANDROID DEVICE**: after you have *added a calendar by URL* in your Google Calendar on a Desktop Computer, you should then find that calendar synchronized with your Google account, so the calendar should become available to any Android Calendar apps that have access to your Google account to synchronize calendars.
* **IPHONE**: go to **`Phone Settings`** -> **`Accounts`** -> **`Add account`** -> **`Other`** -> **`Add Calendar`**, and paste in the endpoint URL with the desired parameters, (make sure you use **`ICS`** as value of the *`returntype`* parameter). And remember, if you omit the *`year`* parameter, it will use the current year. This should mean that as the iPhone Calendar continues to poll the calendar URL, on the turn of a new year new events should be created automatically for the new year.
* **MICROSOFT OUTLOOK** *(tested with Outlook 2013)*: at the bottom of the screen, switch from **`Email`** view to **`Calendar`** view. On the ribbon of the **`Home`** menu item, click on **`Open calendar`** -> **`From the internet`**. Paste the endpoint URL with the desired parameters, (make sure you use **`ICS`** as value of the *`returntype`* parameter). And remember, if you omit the *`year`* parameter, it will use the current year. On the following screen, check the checkbox along the lines of "Poll this calendar in the interval suggested by the creator", which would mean that Outlook Calendar should poll the calendar URL once a day. This means that without the *`year`* parameter, on the turn of a new year new events should be created automatically for the new year. Make sure the Calendar is created in the **`Other calendars`** folder; if you find it under the **`Personal calendars`** folder, drag it and drop it onto the **`Other calendars`** folder, this should ensure that it is treated as a subscription internet calendar. You can manually trigger an update against the calendar URL by clicking on **`Send/receive all`** (from the **`SEND/RECEIVE`** menu item). One highlight of the calendar in Outlook is that it supports a minimal amount of HTML in the event description, so the event descriptions in the Liturgical Calendar are a little bit more "beautified" for Outlook.

## Structure of the data returned by requests to the endpoint
For simplicity we will only take into consideration the structure of a response with JSON data.
Two object keys are returned:
1. **`LitCal`**: has a value which is an object who's key => value pairs reflect the liturgical events generated for the calendar requested. Example value of the `LitCal` key (limited to two of the generated events):
```javascript
  "LitCal":{
    "MotherGod":{
      "name":"SOLLEMNITAS SANCTAE DEI GENITRICIS MARIAE",
      "color":"white",
      "type":"fixed",
      "grade":6,
      "common":"",
      "date":"1577836800",
      "liturgicalyear":"ANNUM A"
    },
    "StsBasilGreg":{
      "name":"Sancti Basilii Magni et Gregorii Nazianzeni, episcoporum et Ecclesiae doctorum",
      "color":"white",
      "type":"fixed",
      "grade":3,
      "common":"Proper",
      "date":"1577923200"
    }
  }
```
Each of the events generated is represented as an object whose key => value pairs give the necessary information about the liturgical event:
  * `name`   : A localized string with the full name of the liturgical event ready for display
  * `color`  : The liturgical color associated with this liturgical event. Some events might have multiple possible liturgical colors, for examples memorials of saints where there is a choice between Commons. In these cases, the multiple possible colors are separated by a pipe character `|` in the same order as the relative possible Commons indicated in the `common` property. The color value is always in English, so that it can be used as is for generation of CSS styling rules especially if used as inline styles. Localization of these values is up to the requesting application which is in charge of creating the display, for example a value of `red|white` can be split on the pipe character and these values used for CSS styling, and a string could be created in Latin for display purposes: `'ruber vel album'`
  * `type`   : The type of celebration, whether *fixed* or *mobile* (*fixed* celebrations have the same date every year, *mobile* celebrations are calculated either based on the date of Easter or because they always fall on the same day of the week within a specific time frame)
  * `grade`  : The logical importance of the celebration, represented as a number from 0 to 7, used to calculate precedence compared to other possible events. The importance or precedence value will determine whether one event may suppress another event or have it moved to the next possible open slot according to certain criteria. A general association with liturgical terminology could be something like this:
    - `0` = WEEKDAY
    - `1` = COMMEMORATION
    - `2` = OPTIONAL MEMORIAL
    - `3` = MEMORIAL
    - `4` = FEAST
    - `5` = FEAST OF THE LORD
    - `6` = SOLEMNITY
    - `7` = event that has precedence over a solemnity
    
  However this association is not suitable for displaying the actual *grade* (or *'rank'*) of the festivity in liturgical terms, because some events have a logical importance that does not correspond with their portrayed grade, for example "All Souls Day" is called a "Commemoration" and yet it is given the same importance as a solemnity. Thus "All Souls Day" will have a grade of 6, but should be displayed as "Commemoration" rather than as "Solemnity". Perhaps a future update to the engine will add a "displayGrade" property with the actual liturgical grade associated with the event, suitable for display.
  * `common` : Indicates whether the liturgical texts for the celebration (in the case of memorials of saints) can be found in the Proper of Saints in the Roman Missal, or whether in the various Commons. In the former case the value will be simply `Proper`, in the latter case there will be a more complex construct:
    - if it is possible to use liturgical texts from more than one common, the multiple possible commons will be listed as a comma separated list `,`
    - each common has multiple categories of persons, with liturgical texts that are suitable for the specific category. The common and the specific category within the common will be separated by a colon `:`
  An example value of the `common` property: `"Pastors:For a Bishop|Doctors"`. This means that it is possible to choose the liturgical texts either from the *Common of Pastors* or from the *Common of Doctors*; in the former case, the liturgical texts should be taken from the specific category *For a Bishop*. A textual representation ready for display would be something like this: *From the common of Pastors: For a Bishop; or from the Common of Doctors*. Please refer to the example scripts, whether the PHP example or the Javascript example, in order to understand better how to handle the interpretation and localization of these values, with all possible cases. 
  * `date`   : a PHP style unix timestamp in UTC time. The actual time (hours, minutes, seconds) should be a zero value seeing that we deal only with all day events, and time is not of importance. For use in **Javascript**, multiply this value by 1000, because Javascript uses `milliseconds` whereas **PHP** uses `seconds` as a base for a UNIX timestamp. The timestamp value should be dealt with accordingly in each programming language used: as is if the language uses seconds as a base, or multiplying by 1000 if it uses milliseconds as a base.
  * `liturgicalyear` : the cycle of liturgical years that this event corresponds to. This property will only be present for events where it is applicable (Sundays and Weekdays of Ordinary Time or those liturgical events whose texts are based on the liturgical cycle), as can be noted in the sample data above. When present, it will have a localized value of `YEAR A`, `YEAR B`, or `YEAR C` for festive events or a value of `YEAR I`, `YEAR II` for weekday events (if an application makes a request for the Italian language, the values will contain `ANNO` instead of `YEAR`, and likewise for any localization requested).
2. **`Settings`**: has a value which is an object who's key => value pairs reflect the settings used in the request to produce this specific calendar. These are useful more or less just as feedback so that we can be sure that the calendar was effectively produced with the requesting settings. Example value of the `Settings` key:
```javascript
  "Settings":{
    "YEAR":2020,
    "EPIPHANY":"JAN6",
    "ASCENSION":"SUNDAY",
    "CORPUSCHRISTI":"SUNDAY",
    "LOCALE":"LA",
    "returntype":"JSON"
  }
```

3. **`Messages`**: has a value which is an array containing all of the significant operations done in the calculation of the requested Liturgical Calendar, with links to the Decrees of the Congregation for Divine Worship where applicable. Useful for understanding how or why the calculations were done, and what changes have been applied in the generation of the Calendar. A small sample portion of data that can be returned:

```Javascript
"Messages": [
    "<i>'4th Sunday of Ordinary Time'<\/i> is superseded by a Solemnity in the year 2020.",
    "<i>'31st Sunday of Ordinary Time'<\/i> is superseded by a Solemnity in the year 2020.",
    "<i>'11th Sunday of Ordinary Time'<\/i> is superseded by a Solemnity in the year 2020.",
    "The Feast <i>'Saints Philip and James, Apostles'<\/i>, usually celebrated on May 3rd, is suppressed by a Sunday or a Solemnity in the year 2020.",
    "The Feast <i>'Visitation of the Blessed Virgin Mary'<\/i>, usually celebrated on May 31st, is suppressed by a Sunday or a Solemnity in the year 2020.",
    "The Feast <i>'Saint Luke the Evangelist'<\/i>, usually celebrated on October 18th, is suppressed by a Sunday or a Solemnity in the year 2020.",
    "The Feast <i>'Saint John, Apostle and Evangelist'<\/i>, usually celebrated on December 27th, is suppressed by a Sunday or a Solemnity in the year 2020.",
    "<i>'First day before Epiphany'<\/i> is superseded the Memorial <i>'Saints Basil the Great and Gregory Nazianzen, bishops and doctors'<\/i> in the year 2020.",
    "The Memorial <i>'Saints Timothy and Titus, bishops'<\/i>, usually celebrated on January 26th, is suppressed by a Sunday, a Solemnity or a Feast in the year 2020.",
    "The Memorial <i>'Saint Polycarp, bishop and martyr'<\/i>, usually celebrated on February 23rd, is suppressed by a Sunday, a Solemnity or a Feast in the year 2020.",
    "The Memorial <i>'Saints Perpetua and Felicity, martyrs'<\/i> falls within the Lenten season in the year 2020, rank reduced to Commemoration.",
    "The Memorial <i>'Saint John Baptist de la Salle, priest'<\/i> falls within the Lenten season in the year 2020, rank reduced to Commemoration."
]
```

# CHANGELOG

## [v2.8](https://github.com/JohnRDOrazio/LiturgicalCalendar/releases/tag/v2.8) (August 11th 2020)
 * adds `diocesanpreset` and `nationalpreset` parameters with relative calendar data
 * adds all of the data from the recent Decrees of the Congregation for Divine Worship and verifies integrity with past Decrees
 * ensures `Messages` returned are as specific as possible, while trying to keep the code as clean as possible
 * adds FullCalendar example

## [v2.7](https://github.com/JohnRDOrazio/LiturgicalCalendar/releases/tag/v2.7) (July 28th 2020)
 * adds `Messages` array to the data that is generated by the endpoint, justifying the calculations made for the generation of the requested calendar
 * fixes an issue with the memorial Saint Jane Frances de Chantal after 1999, when it was decided to move the memorial from Dec. 12 to Aug. 12 in order to allow Our Lady of Guadalupe on Dec. 12 (if another more important celebration took place on Dec. 12, Saint Jane Frances was being removed before it could be moved, this is now handled correctly)
add translations for the Messages array in Italian, English and Latin (please forgive my macaronic latin, it's not at all perfect, it's mostly conjecture, I hope to have it proofread at some point)
 * update PHP example to display `Messages` array in a table below the generated calendar
 * update PHP example to fix parsing of Liturgical colors for memorials with more than one possible Common and more than one possible liturgical color
 * fix a few errors in the database as regards liturgical colors for some memorials with more than one possible Common

## [v2.6](https://github.com/JohnRDOrazio/LiturgicalCalendar/releases/tag/v2.6) (July 26th 2020)
 * integrate the calculation of the liturgical cycle (YEAR A,B,C for festivities and YEAR I,II for weekdays) directly into the engine, so that applications that take care of elaborating the data for display don't have to worry about it
 * update both examples, PHP and Javascript, to use the new `liturgicalyear` property returned in the JSON data, and bring Javascript example up to par with the PHP example (add month cell that spans all events for that month)

## [v2.5](https://github.com/JohnRDOrazio/LiturgicalCalendar/releases/tag/v2.5) (July 25th 2020)
 * make sure all endpoint parameters can have values with either uppercase or lowercase characters
 * fix a few small issues with the ICS data generation

## [v2.4](https://github.com/JohnRDOrazio/LiturgicalCalendar/releases/tag/v2.4) (July 24th 2020)
 * move as many festivities as possible to the MySQL tables to allow for localization (mobile feasts will be calculated in the script, but still need to be localized)
 * add ICS data generation (requires more localization strings, because it is already a form of final display of the data)

## [v2.0](https://github.com/JohnRDOrazio/LiturgicalCalendar/releases/tag/1.0) (January 8th 2018)
 * separate the display logic from the engine, so that the engine can act as an endpoint
 * make the engine return JSON or XML data that the display logic can use to generate a user-friendly representation of the data

## [v1.0](https://github.com/JohnRDOrazio/LiturgicalCalendar/releases/tag/1.0) (July 26th 2017)
 * proof of concept for the correct generation of a liturgical calendar
 * create MySQL table for the Proper of the Saints
