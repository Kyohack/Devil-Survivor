================================
Devil Survivor Savegame Editor
================================



Description:
------------
This is a web-based save editor for the Devil Survivor DS game. Currently only raw savegame files are supported, but other formats will be allowed soon. The savegame editor is hosted at: http://pokecheats.net/tools/devils-survivor-savegame-editor.php



Contributors:
-------------
~CollosalPokemon

~Kyohack



Threads:
-------
http://pokecheats.net/forum/showthread.php?14686-Devil-Survivor-Savegame-Editor

http://gbatemp.net/threads/346197/



To do:
------
~Research checksum algorithm (current algorithm only allows for small edits/changes).

~Research leader/demon names.

~Add field to edit money "macca" (int32 at 0x484).

~Add field to edit shop rating (int32 at 0x488).

~Add field to edit player names (Shift-JIS encoded text at 0x041D).

~Research a possible (epoch?) timestamp (int32 at 0x92C).

~Research quick save data (0x9C8 to end of file).
