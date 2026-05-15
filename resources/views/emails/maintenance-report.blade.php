<x-mail::message>
# High Priority: Mechanical Alert

A new maintenance issue has been reported by **{{ $record->driver->name }}**.

<x-mail::panel>
**Vehicle:** {{ $record->vehicle->name }} ({{ $record->vehicle->license_plate }})  
**Issue Type:** {{ $record->issue_type }}  
**Status:** {{ $record->status }}
</x-mail::panel>

### Driver Description:
{{ $record->description }}

<x-mail::button :url="config('app.url') . '/maintenance'">
View Fleet Maintenance
</x-mail::button>

Safe travels,<br>
{{ config('app.name') }} Fleet Command
</x-mail::message>
