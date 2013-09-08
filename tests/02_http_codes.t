<?php
	include(dirname(__FILE__).'/wrapper.php');

	plan(150);

	loadlib('http');



	#
	# test return codes
	#

	$codes = array(
		array(100, "Continue"),
		array(101, "Switching Protocols"),
		array(102, "Processing", "WebDAV; RFC 2518"),
		array(200, "OK"),
		array(201, "Created"),
		array(202, "Accepted"),
		array(203, "Non-Authoritative Information", "since HTTP/1.1"),
		array(204, "No Content"),
		array(205, "Reset Content"),
		array(206, "Partial Content"),
		array(207, "Multi-Status", "WebDAV; RFC 4918"),
		array(208, "Already Reported", "WebDAV; RFC 5842"),
		array(226, "IM Used", "RFC 3229"),
		array(300, "Multiple Choices"),
		array(301, "Moved Permanently"),
		array(302, "Found"),
		array(303, "See Other", "since HTTP/1.1"),
		array(304, "Not Modified"),
		array(305, "Use Proxy", "since HTTP/1.1"),
		array(306, "Switch Proxy"),
		array(307, "Temporary Redirect", "since HTTP/1.1"),
		array(308, "Permanent Redirect", "approved as experimental RFC"),
		array(400, "Bad Request"),
		array(401, "Unauthorized"),
		array(402, "Payment Required"),
		array(403, "Forbidden"),
		array(404, "Not Found"),
		array(405, "Method Not Allowed"),
		array(406, "Not Acceptable"),
		array(407, "Proxy Authentication Required"),
		array(408, "Request Timeout"),
		array(409, "Conflict"),
		array(410, "Gone"),
		array(411, "Length Required"),
		array(412, "Precondition Failed"),
		array(413, "Request Entity Too Large"),
		array(414, "Request-URI Too Long"),
		array(415, "Unsupported Media Type"),
		array(416, "Requested Range Not Satisfiable"),
		array(417, "Expectation Failed"),
		array(418, "I'm a teapot", "RFC 2324"),
		array(420, "Enhance Your Calm", "Twitter"),
		array(422, "Unprocessable Entity", "WebDAV; RFC 4918"),
		array(423, "Locked", "WebDAV; RFC 4918"),
		array(424, "Failed Dependency", "WebDAV; RFC 4918"),
		array(424, "Method Failure", "WebDAV"),
		array(425, "Unordered Collection", "Internet draft"),
		array(426, "Upgrade Required", "RFC 2817"),
		array(428, "Precondition Required", "RFC 6585"),
		array(429, "Too Many Requests", "RFC 6585"),
		array(431, "Request Header Fields Too Large", "RFC 6585"),
		array(444, "No Response", "Nginx"),
		array(449, "Retry With", "Microsoft"),
		array(450, "Blocked by Windows Parental Controls", "Microsoft"),
		array(451, "Unavailable For Legal Reasons", "Internet draft"),
		array(451, "Redirect", "Microsoft"),
		array(494, "Request Header Too Large", "Nginx"),
		array(495, "Cert Error", "Nginx"),
		array(496, "No Cert", "Nginx"),
		array(497, "HTTP to HTTPS", "Nginx"),
		array(499, "Client Closed Request", "Nginx"),
		array(500, "Internal Server Error"),
		array(501, "Not Implemented"),
		array(502, "Bad Gateway"),
		array(503, "Service Unavailable"),
		array(504, "Gateway Timeout"),
		array(505, "HTTP Version Not Supported"),
		array(506, "Variant Also Negotiates", "RFC 2295"),
		array(507, "Insufficient Storage", "WebDAV; RFC 4918"),
		array(508, "Loop Detected", "WebDAV; RFC 5842"),
		array(509, "Bandwidth Limit Exceeded", "Apache bw/limited extension"),
		array(510, "Not Extended", "RFC 2774"),
		array(511, "Network Authentication Required", "RFC 6585"),
		array(598, "Network read timeout error"),
		array(599, "Network connect timeout error"),
	);

	foreach ($codes as $row){

		$ret = http_get("http://www.iamcal.com/misc/test/code.php?code={$row[0]}&msg=".urlencode($row[1]));

		$test = "{$row[0]}: {$row[1]}";
		if (isset($row[2])) $test .= " {$row[2]}";

		is($ret['ok'], ($row[0] < 200) || ($row[0] > 299) ? 0 : 1, "OK for {$test}");
		is($ret['code'], $row[0], "Status code for {$test}");

		flush();
	}
