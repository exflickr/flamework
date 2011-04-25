Troubleshooting
===============

* __Login cookies don't work__

  You may need to set your <code>abs_root_url</code> setting manually. <code>auth_cookie_domains</code> will
  try and pull the domain part from the root URL of your app. Modify it directly to set cookies at a different
  subdomain. Cookie paths are not supported (but we should fix that).


* __No email gets delivered__

  Have you set <code>auto_email_args</code> in your config? Your mail server may block delivery of mail
  with envelopes from domains that it does not control.
