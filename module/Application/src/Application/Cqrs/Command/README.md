CQRS Commands
=============

Commands are used to trigger state changes in the domain. You get no result, when you
invoke a command, but you can listen on a Domain Event, that signals that a state has
been changed. Each command should have a corresponding event.
Commands are named in the imperative: DoSomethingCommand.