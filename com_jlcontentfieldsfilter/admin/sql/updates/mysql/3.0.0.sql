-- Migration script for JL Content Fields Filter component
-- For upgrades from 3.x to 4.x - Joomla 6 compatibility update

-- Rename column from 'publish' to 'state' (only runs on 3.x upgrades)
ALTER TABLE `#__jlcontentfieldsfilter_data` 
    CHANGE COLUMN `publish` `state` int NOT NULL DEFAULT 1;

-- Update meta fields to TEXT type for longer content support (except meta_title)
ALTER TABLE `#__jlcontentfieldsfilter_data` 
    MODIFY COLUMN `meta_desc` TEXT NOT NULL,
    MODIFY COLUMN `meta_keywords` TEXT NOT NULL;