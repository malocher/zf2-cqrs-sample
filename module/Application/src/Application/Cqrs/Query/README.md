CQRS Queries
============

Queries are used to get information out of the ReadModel.Other than invoking a command,
the execution of a query has a direct result.

If you deal with lots of data, your domain model can get very slow,
f.e. when you try to display statistics of the relationship of your entities.

With a seperated ReadModel it is easier to focus on performance, but you don't want to
duplicate domain logic. CQRS helps you out. Your ReadModel can listen on Events and keeps 
it view information up to date. If a query is executed, your ReadModel simply returns the
current snap shot of the application state.

It is up to you if you want to
seperate your ReadModel from your WriteModel. When using queries you can change it
at any time. 