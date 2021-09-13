create or replace function periodickey_generate
(
	p_period int,
	p_type varchar
)	
returns int as $$
declare
	v_id int;
begin
	insert into	"periodickey"
	select		p_period, p_type, 0
    where		not exists 
    (
        select		"periodickeyid"
        from		"periodickey"
        where		"periodickeyperiod" = p_period
		and			"periodickeytype" = p_type
    )
    on conflict ("periodickeyperiod", "periodickeytype")
    do nothing;

  update	"periodickey"
	set			"periodickeyid" = "periodickeyid" + 1
	where		"periodickeyperiod" = p_period
	and			"periodickeytype" = p_type
	returning	"periodickeyid"
	into		v_id;
    
   	return v_id;
end;
$$ language plpgsql;