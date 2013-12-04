CQRS Buses
===========

CQRS Buses manage the communication within a cqrs system. Controller and other 
external consumers use them to trigger state changes or get information out of the system.

In the malocher/cqrs-esb implementation it is possible to use many buses. This is useful
if you want to seperate different parts of your domain, or you work with different modules 
and each module has it's own bus. See [malocher/cqrs-esb iterations](https://github.com/malocher/cqrs-esb/tree/master/iterations/Iteration)
for more details.