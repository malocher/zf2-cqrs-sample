Namespace structure
===================

### Controller

Application Controller

### Cqrs

Contains all cqrs stuff:
- the used bus
- commands, queries and events
- payload objects

This namespace could also be called API, cause the controllers or external systems
only communicate via cqrs messages with the domain and the read model.

### Domain

Only our write model uses the Domain Driven Design pattern, so we have put it under
a different namespace then the read model. All commands get to the Domain and all
events come out of the Domain.

### Form

ZF2 form objects, containing validation rules and frontend information

### ReadModel

The ReadModel listen on Domain Events and updates it's view information

