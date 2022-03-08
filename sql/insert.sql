-- #########################
-- MEMBERS - Insert values
-- #########################
INSERT INTO public.members (username, password) VALUES ('alice','$2y$10$L142WyHMwZ//687TAfoRzOqiROu9lXEHF.dHdfUsfBOARFPMN9y5W');
INSERT INTO public.members (username, password) VALUES ('martin','$2y$10$4jOC1aJzgSoiFXxS/oHuTeBvHIM68Rw62WneJxfDUJ6T3lonBasTy');
INSERT INTO public.members (username, password) VALUES ('jonas','$2y$10$5xs8TDkCygxnqgI7Rel4uerXyQo8ZcDiLiTGModYuHeqtue4EV/0y');
INSERT INTO public.members (username, password) VALUES ('mohammed','$2y$10$4jOC1aJzgSoiFXxS/oHuTeBvHIM68Rw62WneJxfDUJ6T3lonBasTy');
INSERT INTO public.members (username, password) VALUES ('sanna','$2y$10$o3UrEufFNDtzo92egIVMXe7EsgaYwAOMe85grONwdXaEWe1eQkwhi');
INSERT INTO public.members (username, password) VALUES ('kajsa','$2y$10$HgDaz3a6lDDN/X7epW3bgugubd2Ob8BsSSRP907vDFjh5Cl.tg6yC');
INSERT INTO public.members (username, password) VALUES ('lucas','$2y$10$4CdEutmGfX7OIjOr/G5eG.TKk4vTuT8dyW28aIOggdwxsuDGRl.ji');
INSERT INTO public.members (username, password) VALUES ('björn','$2y$10$JYVdlKN/WGBt6uGx/b3fMO4HYmSCvOEJbynBFTohfUHJT0DMppeR.');
INSERT INTO public.members (username, password) VALUES ('carina','$2y$10$Mmpg6jw8mWTWfOBrGCZ2MuyV0ddYHvIkZt17CCXlkU2nvv6rzZj/a');
INSERT INTO public.members (username, password) VALUES ('fredrik','$2y$10$x2d7Z/qa2FCCNE0gjuciyeuDNr4Woe01/lB9BAto32SHgR.bQGbkC');
-- 10 entries

-- #########################
-- TOPICS - Insert values
-- #########################
INSERT INTO public.topics(title, content, timestamp, member_id)
VALUES ('Latest space exploration',
        'NASA’s Curiosity Mars rover has started a road trip that will continue through the summer across roughly a mile (1.6 kilometers) of terrain. By trip’s end, the rover will be able to ascend to the next section of the 3-mile-tall Martian (5-kilometer-tall) mountain it’s been exploring since 2014, searching for conditions that may have supported ancient microbial life.',
        '2019-02-22 04:22:41', 1);

INSERT INTO public.topics(title, content, timestamp, member_id)
VALUES ('Cost of Electric Cars',
        'We all care about the environment so my family has decided to switch to an electric car if we can afford it. What are the costs involved? Is it worth it? I still love a fast car so I''m looking for something quick and fun that is still environmentally friendly. ',
        '2020-01-22 08:10:27', 9);

INSERT INTO public.topics(title, content, timestamp, member_id)
VALUES ('Weird Australian animals',
        'Let''s talk about weird animals in the crazy continent!',
        '2016-07-22 14:14:08', 7);
-- 3 entries

-- #########################
-- POSTS - Insert values
-- #########################
--Weird Australian animals
INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('7',
        'I''ll start! The Thorny Devil is a lizard whose body is covered with little horns. It feeds mainly on ants (about 2000 per meal), cached with the tongue. It lives in arid and sandy areas of Australia and buries in the sand to protect itself from the heat.',
        '2016-07-22 14:16:15' , 3);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('6',
        'The largest living carnivorous marsupial, the Tasmanian Devil, was named after its impressive jaw and terrifying screams when hunting. Previously hunted and eaten by settlers, the Devil is now a threatened species despite its protection (since 1941). Since the 1990s the breed is affected by a form of cancerous facial tumor which is 100% fatal. Nearly 70% of the population was decimated since the onset of the disease. Today the government is trying to save the species and will probably transfer a part of the healthy population to another Australian island…',
        '2016-07-22 14:18:05' , 3);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('8',
        'The Cassowary! This bird looks suspiciously like a prehistoric ostrich. It is only found in the rainforests of Northeastern Australia (only 1200 specimens in the wild). It measures about 1,70 m and weights about 70 kg … Its legs are powerful and have a big claw so better avoid confrontation! They are very territorial and have injured some visitors in the past!',
        '2016-07-22 14:22:27' , 3);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('9',
        'The Kookaburra bird is particularly known for its strange screams, a mocking laugh! In the Aboriginal culture, this bird is mythical. It feeds on anything that crawls, flies and swims!',
        '2016-07-22 17:19:26' , 3);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('1',
        'Fitzroy River Turtle is a freshwater turtle and well known for its ability to breathe through its bum. This special adaptation enables it to remain underwater for an incredible 21 days at a time to feed underwater for longer periods and hide from predators. This turtle can only be found in the Fitzroy Basin in south-eastern Queensland. Sadly, feral animals like foxes, cats and pigs, as well as pollution, murky water and sedimentation have rendered them Vulnerable according to the IUCN list of threatened species.',
        '2016-07-22 22:00:09' , 3);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('2',
        'What’s scarier than a 60kg modern ‘dinosaur’ with killer claws? One that can leap 1.5 metres off the ground. To get the most out of their toe daggers, cassowaries can jump feet first, so their claws can slash downward in mid-air towards their target. They’re great sprinters too, with a top running speed of 50 km/h through dense forest. Not only that, they’re good swimmers, with the ability to cross wide rivers and swim in the sea. That’s one animal you don’t want to be chased by!',
        '2016-07-23 00:07:37' , 3);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('9',
        'The Echidna has porcupine-like spines, a bird-like beak, quoll-like pouch and lays eggs like a reptile. It also feeds their young on milk (like all mammals) but have no nipples – the milk just oozes out of the skin in the pouch and the puggle (baby echidna) licks it up. ',
        '2016-07-24 07:15:43' , 3);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('3',
        'Laughing Kookaburras. Apparently the members of this bird family laugh in a similar manner.',
        '2016-07-24 14:44:37' , 3);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('6',
        'The Numbat is an endangered small marsupial that survives in southwest Western Australia. Due to its small size, the Numbat is hunted by many animals like feral cats, foxes, dingoes and birds of prey. Because it solely on termites which are active by day, the Numbat is the only diurnal (opposite of nocturnal) marsupial.',
        '2016-07-25 18:52:12' , 3);
-- 9 entries

-- Latest space exploration
INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('2',
        'On Wednesday, Curiosity successfully drove a whopping 102.5 meters over 159 minutes. This isn’t the longest drive Curiosity’s ever completed (the record is 142.5 meters on sol 665), but it did set a record for the longest drive ever planned from our quarantined dining room tables and couches!',
        '2019-02-22 08:10:02' , 1);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('1',
        'In this uplink plan Curiosity will do several observations at its current location, then do a relatively long drive on the second sol, followed by additional observation activities. ChemCam and Mastcam will make observations of bedrock targets “Caldback” (rubbly textured) and “Portencross” (smooth). Mastcam will also take stereo images of pebbles and of “Windy Gyle,” an outcrop to the east. Curiosity will then take its drive (hoping to go a distance of over 100 meters) combining an initial drive on terrain we can see with autonomous driving in the later part on terrain we haven’t yet imaged. The drive will be followed by a Sun tau observation by Mastcam and by post-drive image documentation. MARDI will take an image of the ground at twilight.',
        '2019-02-22 08:20:25' , 1);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('5',
        'Also! On the second sol, ChemCam will make a passive observation of the sky to measure its dust and water-vapor content, and will make an observation of a bedrock target selected autonomously by the rover. RAD, REMS, and DAN will continue taking data. Navcam will take a suprahorizon movie, and Mastcam will take another Sun tau measurement to check atmospheric dust.',
        '2019-02-22 09:40:20' , 1);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('6',
        'NASA''s Curiosity rover captured its highest-resolution panorama of the Martian surface between Nov. 24 and Dec. 1, 2019. A version without the rover contains nearly 1.8 billion pixels; a version with the rover contains nearly 650 million pixels.',
        '2019-03-01 09:40:46' , 1);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('2',
        'Yup! It is composed of more than 1,000 images taken during the 2019 Thanksgiving holiday and was carefully assembled over the ensuing months, the composite contains 1.8 billion pixels of Martian landscape.',
        '2019-03-01 10:13:33' , 1);
-- 5 entries


-- Cost of Electric Cars
INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('5',
        'Just like traditional fuel engine vehicles, the cost of buying and running an electric vehicle varies depending on the model, make and specifics of the vehicle – it means there''s an option for everyone. Good news – electric vehicles are likely to cost you less over the course of ownership. Electricity costs much less than petrol or diesel and electric cars require less maintenance than an internal combustion engine (ICE).',
        '2020-01-22 10:40:25' , 2);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('4',
        'The cost of charging your electric car at a public charge point depends on the charge point network and the location of charge points. Many local authorities offer a pay per session approach to on-street chargers. Occasionally they can be free to use if you have access to a network subscription.',
        '2020-01-22 11:19:25' , 2);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('3',
        '',
        '2020-01-22 11:19:25' , 2);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('5',
        'Anyone know how much it costs to buy an electric car?',
        '2020-01-22 11:19:25' , 2);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('8',
        'Currently, one of the cheapest electric cars you can get is the New Renault Zoe, with a new 50kW battery and a range of 245 miles. The New Zoe replaces the older cheaper model of around £14,000 which had a range of around 160 miles. There''s the ever popular Nissan Leaf, which offers anything between 168 miles for the entry level option, to 239 miles for the e+ option. There are also bigger family models like the MG ZS EV that will run for the same mileage of around 163 but have a lot more space in them for all your family needs. A mid-range saloon option is the Hyundai IONIQ which is also 100% electric and has a range of up to 194 miles on a single charge. Higher on the price spectrum there are sportier models like the Tesla Model S',
        '2020-01-22 11:19:25' , 2);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('5',
        'And in addition to environmental benefits... While the price of an EV may be similar to most comparable petrol or diesel cars, the cost of running one is significantly cheaper particularly over the full lifetime of the vehicle.',
        '2020-01-22 11:19:25' , 2);

INSERT INTO public.posts (member_id, content, timestamp , topic_id )
VALUES ('2',
        'And one of the first things I notice when switching to an electric car is the quietness of the vehicle',
        '2020-01-22 11:19:25' , 2);
-- 7 entries