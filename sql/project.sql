-- ##############################################
-- Projekt, Mjukvarusäkerhet
-- Monique Axt
-- ##############################################

CREATE SCHEMA IF NOT EXISTS public;

-- ##############################################
-- Create member table
-- ##############################################
DROP TABLE IF EXISTS public.members CASCADE;
CREATE TABLE public.members
(
    id       SERIAL PRIMARY KEY,
    username text NOT NULL CHECK (username <> ''),
    password text NOT NULL CHECK (password <> ''),
    CONSTRAINT unique_user UNIQUE (username)
)
    WITHOUT OIDS;


-- ##############################################
-- Create topics table
-- ##############################################
DROP TABLE IF EXISTS public.topics CASCADE;
CREATE TABLE public.topics
(
    id        SERIAL PRIMARY KEY,
    title     text        NOT NULL,
    content   text,
    timestamp timestamptz NOT NULL,
    member_id integer     NOT NULL REFERENCES public.members (id) ON DELETE CASCADE
)
    WITHOUT OIDS;

-- ##############################################
-- Create posts table
-- ##############################################
DROP TABLE IF EXISTS public.posts CASCADE;
CREATE TABLE public.posts
(
    id        SERIAL PRIMARY KEY,
    member_id integer     NOT NULL REFERENCES public.members (id) ON DELETE CASCADE,
    content   text        NOT NULL,
    timestamp timestamp   NOT NULL,
    topic_id  integer     NOT NULL REFERENCES public.topics (id) ON DELETE CASCADE
)
    WITHOUT OIDS;


-- ##############################################
-- Create post_votes table
-- ##############################################
DROP TABLE IF EXISTS public.post_votes CASCADE;
CREATE TABLE public.post_votes
(
    post_id   integer NOT NULL REFERENCES public.posts (id) ON DELETE CASCADE,
    member_id integer NOT NULL REFERENCES public.members (id) ON DELETE CASCADE,
    vote      integer,
    PRIMARY KEY (post_id, member_id)
)
    WITHOUT OIDS;
