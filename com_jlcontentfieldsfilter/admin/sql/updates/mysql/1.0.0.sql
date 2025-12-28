-- Migration script from publish to state column
-- For JL Content Fields Filter component
-- Joomla 6 compatibility update

-- Rename column from 'publish' to 'state'
ALTER TABLE `#__jlcontentfieldsfilter_data` 
    CHANGE COLUMN `publish` `state` int NOT NULL DEFAULT 1;

-- Optional: Update any legacy records if needed
-- UPDATE `#__jlcontentfieldsfilter_data` SET `state` = 1 WHERE `state` IS NULL;
