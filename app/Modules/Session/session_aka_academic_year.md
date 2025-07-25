### Why only Create and Read for AcademicYear?

Academic years are immutable records once created, 
since modifying dates could break historical consistency. 
Changing the current session is handled by creating a new one,
which automatically sets the previous session’s status to `TERMINÉ`.
Deletion is avoided to preserve data integrity across related models like classes or assignments.
Therefore, only `Create` and `Read` operations are necessary.
