Domain Structure
=================

We have a simple structure in our sample application. In a more complex application 
you would have aggregates, services and so on. But this is not relevant for using
CQRS. How you structure your model is up to you. Just tell CQRS wich classes in your
model act as CommandHandlers, QueryHandlers and EventListeners.
In our case the commands are routed to an extra commandHandler. You can compare the 
TodoCommandHandler with a MVC Controller for the domain.
In a non CQRS system your controllers would access your domain directly. If not only
the application controllers uses the domain but also other contexts and
all consumers must know how to use your domain. 
With CQRS you close your domain. The only public API is defined by your commands, queries
and events. The implementation is hidden behind the CQRS layer and can be changed 
without side effects.