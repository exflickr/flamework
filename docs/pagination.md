# Pagination

Flamework provides helper functions for creating simple or complex pagaing widgets.

Assuming you've used one of the paginated database fetching functions, all you need to do to 
paginate results is to add the following into a template:

    {pagination}

To make paging appear at the top and bottom of results, simply include the function call in
both places.


## How this works

The `_db_fetch_paginated()` function registers a smarty variable called `$pagination` with 
the following contents:

    array(
    	'total_count' => 100,  # Total number of records
    	'page'        => 1,    # Page we're viewing
    	'per_page'    => 20,   # Number of records per page
    	'page_count'  => 5,    # Total number of pages
    )

Note that because of spill handling, there might be more or less records on the current page
than `per_page` specifies. If you're handling paging yourself, rather than through the DB 
library, then you can pass your own pagintation information to the widget:

    {pagination pagination=$my_data}


## URL formatting

By default, the pagination uses a URL pattern of `/page#`. This means that if your base page
URL is `/foo/bar`, that URL is kept for page 1, while page 2 will be at `/foo/bar/page2`. If
you supplied a pattern of `-p#` then page 2 would be `/foo/bar-p2`. If you would rather pass
page numbers as query string parameters, then you can pass a parameter name to be used. Some 
examples:

    {pagination}
        /foo/bar/page10

    {pagination page_pattern='-p#'}
        /foo/bar-p10

    {pagination page_param='pg'}
        /foo/bar?pg=10

The code is smart about preserving any existing query string arguments in all cases.


## Multiple views

By default, the pagination widget will show page numbers and prev/next links. You can switch to
just prev/next links by adding a `style` param:

    {pagination style='nextprev'}

This just tell the widget to use the template `inc_pagination_nextprev.txt` instead of the default
one (`pretty`). You can add your own paging styles by modifying those templates or adding new ones.


## Keyboard shortcuts

If the config options `pagination_keyboard_shortcuts` or `pagination_touch_shortcuts` are set, then
the left/right cursor keys and back and forward swipe gestures will cause the next/prev page to be
navigated to. Be aware that this will only work for the first pagination widget used on any page.


