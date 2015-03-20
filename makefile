# makefile for alpha.couturecollective.club
# these parameters are stored
# no trailing slash

SSH_USR = salliegiordano
SSH_URL = couturecollective.club
SSH_PORT = 22
PROJ = ~/Work-Space/htdocs/coco
THEME = $(PROJ)/wp-content/themes/coco

LESS_ARGS = --compress --strict-imports


# these parameters should be entered as PARAMNAME=PARAMVALUE
P = password


css:
	lessc $(LESS_ARGS) $(THEME)/_/css/style.less $(THEME)/_/css/style.css

push-beta:
	git push coco-beta +deployment-beta:refs/heads/master

push-alpha:
	git push coco-alpha +deployment-alpha:refs/heads/master


# we need a target for deploying the db
# we need a target for launching uploads
# we need a target for bundling the refspec
# we need to fail if we're not on the proper branch
