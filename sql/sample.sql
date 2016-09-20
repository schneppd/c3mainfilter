--test default insert for id autogen
INSERT INTO ps_c3_mainfilter_selection(name) VALUES ('test motor selection 1'), ('test motor selection 2');
--test manual id
INSERT INTO ps_c3_mainfilter_selection(id_filter_selection, name) VALUES (3, 'test motor selection 3'), (4, 'test tyre selection 1');
INSERT INTO ps_c3_mainfilter_selection(name) VALUES ('test tyre selection 2');
-- create sample filter parts
INSERT INTO ps_c3_mainfilter_selection_part(id_filter_selection, id_feature, id_feature_value, order_part) VALUES 
 (1, 73, 557, 0), (1, 74, 562, 1), (1, 75, 602, 2), (1, 76, 721, 3), (1, 10, 59, 4)
, (2, 73, 556, 0), (2, 74, 564, 1), (2, 75, 604, 2), (2, 76, 724, 3), (2, 10, 48, 4)
, (3, 73, 556, 0), (3, 74, 564, 1), (3, 75, 604, 2), (3, 76, 724, 3), (3, 10, 60, 4)
, (4, 73, 556, 0), (4, 74, 564, 1), (4, 75, 604, 2), (4, 76, 724, 3), (4, 10, 51, 4)
;
--create groups
INSERT INTO ps_c3_mainfilter_selection_group(name, number_step) VALUES ('group1', 5), ('group2', 5), ('group3', 5);
--add filter to groups
INSERT INTO ps_c3_mainfilter_selection_group_member(id_filter_selection_group, id_filter_selection) VALUES
 (1, 1), (1, 2), (2, 3), (3, 4), (3, 1);
--add to categories
INSERT INTO ps_c3_mainfilter_selection_group_shelf(id_filter_selection_group, id_category) VALUES 
 (1, 450), (2, 90);

--delete data if problem
DELETE FROM ps_c3_mainfilter_selection_part;
DELETE FROM ps_c3_mainfilter_selection_group;
ALTER TABLE ps_c3_mainfilter_selection_group AUTO_INCREMENT = 1;