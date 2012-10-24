# Sound of the City REST API

This document explains the purpose, architecture and usage of the SotC REST API.

## Purpose

The project [Sound of the City](http://citysound.itm.uni-luebeck.de) (SotC) was developed at the University of Luebeck, Germany. Its purpose is to provide an authoring, navigation and visualization service for geo-tagged noises and sound feeds. At the core of SotC is a community-based approach to use user's mobile phones to act as live geo-noise and geo-sound sensors. Users can either upload their own content or observe noise levels and sounds by navigating through an interactive map of their surrounding area.

In its initial implementation, the service used SOAP for client-server-communication. [Server One](https://github.com/nepa/Server-One), the media backend of SotC, is available as open-source software on GitHub. Although the repository is outdated, it gives a good impression of the project's capabilities.

With a new REST API, we are now striving to achieve better access to the publicly available data. Interested parties are invited to join the project and build their own applications based on our API. The document at hand describes the internal architecture, setup and usage of the SotC REST API. A developer's guide for the project will be available on the Sound of the City website soon.

## Architecture

First of all, a rewrite rule in `.htaccess` will redirect all REST requests to `index.php`. The API will then create a new `Request` object and parse all parameters that are relevant to processing the request (e.g. URL path and arguments). Afterwards, the request will be passed to `handleRequest()` in the `Dispatcher` class.

The **dispatcher** is responsible for input and output handling. It will load the required resource controller and execute a callback function for the respective HTTP action (GET, POST, PUT or DELETE). Each controller is derived from `BaseController` and is in charge of a specific data model (e.g. noise levels or sound samples). After processing a request, the **resource controller** will return an associative array with the result data.

Next, the dispatcher will pass the result to an **output handler**. Various implementations of the `BaseOutputHandler` exist. All of them provide a `render()` method to format the array which contains the REST response data. Depending on the HTTP `Accept`-header that was sent by the client, the dispatcher will respond with an XML document, a JSON-formatted file or an HTML page respectively.

## Workflow diagram

```
 +--------+            +------------+                            +---------------------+
 |        |+---------->|            |     1. Parse request       |                     |
 | Client |            |  index.php |+-------------------------->|   Request object    |
 |        |<----------+|            |                            |                     |
 +--------+            +------------+                            +---------------------+
                            ^   +
                            |   |
                            |   |                                    +------------+
                            |   |                                    |            |
                            |   |  2. Pass request object            | Data model |
                            |   |                                    |            |
                            |   |                                    +------------+
                            |   |                                        ^   +
                            |   |                                        |   |
                            +   v                                        +   v
                       +------------+     3. Handle request      +---------------------+
                       |            |+-------------------------->|                     |
                       | Dispatcher |                            | Resource Controller |
                       |            |<--------------------------+|                     |
                       +------------+     4. Return response     +---------------------+
                            ^   +
                            |   |
                            |   |         5. Render response     +---------------------+
                            |   +------------------------------->|                     |
                            |                                    |   Output Handler    |
                            +-----------------------------------+|                     |
                                   6. Return XML/JSON/HTML       +---------------------+
```

## Setup

### Installation

To install the SotC REST API, copy all files from the `src` folder to your webserver. You do **not** need to move the files to the same directory where Server-One is located. We recommend to use a folder structure like `https://api.example.com/rest/v1/`, if you want to host multiple versions of the API at the same time.

Next, open the `.htaccess` file and modify the `RewriteBase` entry such, that it points to your installation directory. Then edit `index.php` and set the PHP constant `BACKEND_LOCATION` to the path where your installation of the Server-One service backend resides. The path must be relative to your API location and must **not** have a trailing slash.

Optionally, you can also enable the RewriteRule at the end of the `.htaccess` file to force HTTPS for all REST requests. This requires a valid SSL certificate for your server, though.

When you are running the SotC REST API in a live environment, you should finally disable PHP's error reporting by passing `0` to the `error_reporting()` method at the very beginning of `index.php`. This will prevent hackers from gaining confidential information about the service you are hosting.

### Authentication

Reading access to the REST interface is not limited by and means. Writing access is forbidden, unless the client provides a valid API key in the POST request. Credentials for client authentication, namely a pair of _application name_ and _API key_, are stored in their own database table. The `Authentication` class, which is part of the Server-One code base, will take care of API key generation and validation of authentication data.

API keys are assigned on a per-application basis, not per user. Credentials must be passed in the body of a POST request, in order to authenticate the client. If no valid key is provided, the SotC REST API will issue an error message.

Server-One's `Authentication` class provides a helper method called `createApiKey()`. It can be used to generate new API keys. Right now, the key is just an MD5 hash of the application name, a salt value and the current timestamp. The method, however, does **not** create a new user in the database. It will simply return the key as a string value. You have to add it to the database manually.

## Usage

The initial version of the SotC REST API supports three resources:

  * `noiseLevels`,
  * `soundSamples` and
  * `deviceInfos`

On each resource, various actions can be issued (e.g. `average`, `list`, `report` or `upload`). In most cases the client must provide additional parameters, for example `longitude`/`latitude` or a `zipCode`. These parameters are typically passed as URL arguments. A very important URL parameter is `format` and can be set to either `json`, `xml` or `html`. It will determine what serialization format will be used in the response message.

POST requests can also have a body, which may contain an entire JSON document. This way clients can pass a more complex set of arguments to the service. Credentials for client authentication, for example, are also part of the POST request body.

A simple GET request might look like this:

`https://api.example.com/rest/v1/noiseLevels/list/?format=json&latitude=51.58&longitude=7.6&range=10.0`

The HTTP header of a REST request must always set the content type to JSON, otherwise the API will return an error:

`Content-Type: application/json`

A more comprehensive documentation for end-users will be available on the [Sound of the City](http://citysound.itm.uni-luebeck.de) website soon.

## History

  * **Version 1:**
    * Basic implementation of dispatcher, resource controllers and output handlers
    * REST interface is compatible to Server-One's initial SOAP interface
    * Unit tests for all REST resources

## Author

The SotC REST API was written by: **Sascha Seidel**, NetPanther@gmx.net

Feel free to fork this repository and to modify the project.
