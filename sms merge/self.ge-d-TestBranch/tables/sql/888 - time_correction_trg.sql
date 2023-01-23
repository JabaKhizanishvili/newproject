CREATE OR REPLACE TRIGGER time_correction_trg
  before insert or update on lib_graph_times
  for each row
declare
  -- local variables here
begin
  :NEW.START_TIME  := lpad(:NEW.START_TIME, 5, '0');
  :NEW.END_TIME    := lpad(:NEW.END_TIME, 5, '0');
  :NEW.START_BREAK := lpad(:NEW.START_BREAK, 5, '0');
  :NEW.END_BREAK   := lpad(:NEW.END_BREAK, 5, '0');
end time_correction_trg;
