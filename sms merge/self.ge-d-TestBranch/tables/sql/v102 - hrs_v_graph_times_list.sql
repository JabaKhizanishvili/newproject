create or replace view hrs_v_graph_times_list as
select "ID","GTYPE","GSTART","GDATE"
  from (select t.id, 2000 gtype, t.start_time gstart, t.start_time gdate
          from LIB_GRAPH_TIMES t where t.type=0
        union all
        select t.id, 2500 gtype, t.start_time gstart, t.start_break gdate
          from LIB_GRAPH_TIMES t where t.type=0
        union all
        select t.id, 3000 gtype, t.start_time gstart, t.end_break gdate
          from LIB_GRAPH_TIMES t where t.type=0
        union all
        select t.id, 3500 gtype, t.start_time gstart, t.end_time gdate
          from LIB_GRAPH_TIMES t where t.type=0
        union all
        select 0 id, 1500 gtype, '00:00' gstart, '00:00' gdate from dual t
        ) d
 where d.gdate is not null
 order by d.id, d.gtype
