CQRS Payload Objects
====================

In most cases CQRS messages transport data. This data is called payload. Messages 
in a CQRS system must be immutable. To quarantee the immutability, payload must not
be an object, cause objects are passed by reference other than arrays or scalar values.
The only type of object that is accepted as payload is a Cqrs\Payload\PayloadInterface.
Working with objects as payload can be usefull when you want to pass entities to messages.
Your entities only need to implement the PayloadInterface and provide a getArrayCopy() method.
But be careful: $message->getPayload() doesn't return the entity, it returns the array copy.

Another use case for the PayloadInterface is shown in this example application.
We use it to get an object oriented way of working with the payload.