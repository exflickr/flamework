<?php

	#
	# $Id$
	#

	#################################################################

        # http://code.flickr.com/blog/2010/02/08/ticket-servers-distributed-unique-primary-keys-on-the-cheap/

	#################################################################

        function dbtickets_create($len=32, $poormans_dbtickets=0){

                $table = 'Tickets' . intval($len);

		# As in an instance of flamework that has no access to
		# its mysql config files and/or the ability to set up
		# a dedicated DB server for tickets.

		if ($poormans_dbtickets){

			# ALTER TABLE tbl_name AUTO_INCREMENT = (n)
			# how the fuck do you set the offset from the SQL CLI ?

			$rsp = db_tickets_write("SET @@auto_increment_increment=2");

			if (! $rsp['ok']){
				return null;
			}

			$rsp = db_tickets_write("SET @@auto_increment_offset=1");

			if (! $rsp['ok']){
				return null;
			}
		}

		$rsp = db_tickets_write("REPLACE INTO {$table} (stub) VALUES ('a')");
		return ($rsp['ok']) ? $rsp['insert_id'] : null;
        }

	#################################################################
?>