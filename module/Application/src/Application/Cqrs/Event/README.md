CQRS Events
===========

An Event (Domain Event if you use the DDD pattern) signals a state change. 
Events are published, if a command was successfully invoked (or in case of an ErrorEvent, an error occurred).
It is also possible that more than one event is published after a command was invoked. 
It depends on the implementation.
Events are named in the past: SomethingDidEvent.