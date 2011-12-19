# Flamework Style guide

The coding style is idiosyncratic and will stay that way. There are no
spaces between closing parentheses and opening braces. We indent with
tabs. All functions in a library must start with the library name,
globals too. We don't (often) use constants. An underscore at the
start of a function means it's library-private, same with
globals. Function names are all lowercase, split with underscores. We
don't use objects.

We turn on E_ALL & E_STRICT, but ignore most E_NOTICEs because they're
dumb. We do quote all hash keys, but we don't care about undefined
keys or variables - isset() is vary rarely used.

If you submit patches that don't conform to the weird standards,
they'll get reformatted. It's not you, it's me.
