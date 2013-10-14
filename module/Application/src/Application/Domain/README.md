Domain Structure
=================

We have a simple structure in our sample application. In a more complex application 
you would have aggregates, services and so on. But this is not relevant for using
cqrs. How you structure your model is up to you. Just tell CQRS wich classes in your
model act as CommandHandlers, QueryHandlers and EventListeners.