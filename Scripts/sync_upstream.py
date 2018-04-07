#!/usr/bin/python
import tempfile
import subprocess
import collections
import os
import sys
import ujson
import argparse


def get_commits_branch(branch_name):
    result = collections.OrderedDict()
    with tempfile.NamedTemporaryFile() as output_stream:
        subprocess.call(['git', 'log', branch_name], stdout=output_stream, stderr=open(os.devnull, 'wb'))
        output_stream.seek(0)
        this_commit = None
        for line in output_stream.read().strip().split('\n'):
            if line.startswith('commit '):
                this_commit = line.split()[1]
                result[this_commit] = list()
            if this_commit:
                result[this_commit].append(line)
    return result


def find_sync_point(tgt_commits, src_commits):
    for tgt_commit_hash in tgt_commits:
        for src_commit_hash in src_commits:
            if src_commit_hash == tgt_commit_hash:
                return src_commit_hash
    return None


def git_cherry_pick(commit_hash):
    """ Simple cherry pick procedure, only appy if it fits.
    """
    try:
        subprocess.check_output(['git', 'cherry-pick', commit_hash], stderr=open(os.devnull, 'wb'))
        return True
    except subprocess.CalledProcessError:
        subprocess.check_output(['git', 'cherry-pick', '--abort'])
        return False

def git_checkout(branch_name):
    try:
        subprocess.check_output(['git', 'checkout', branch_name], stderr=open(os.devnull, 'wb'))
        return True
    except subprocess.CalledProcessError:
        return False

def git_add_upstream():
    try:
        subprocess.check_output(['git', 'remote','add','opnsense', 'https://github.com/opnsense/core.git'], stderr=open(os.devnull, 'wb'))
        return True
    except subprocess.CalledProcessError:
        return False

def git_pull_upstream(branch_name):
    try:
        subprocess.check_output(['git', 'checkout', "upstream_%s"%branch_name], stderr=open(os.devnull, 'wb'))
        subprocess.check_output(['git', 'pull', 'opnsense', branch_name], stderr=open(os.devnull, 'wb'))
        subprocess.check_output(['git', 'push', 'origin', "upstream_%s"%branch_name], stderr=open(os.devnull, 'wb'))
        return True
    except subprocess.CalledProcessError:
        return False


def main(branch_name):
    if git_add_upstream():
        print ('add upstream')
    if git_checkout("upstream_%s" % branch_name):
        if git_checkout(branch_name):
            target_commits = get_commits_branch(branch_name)
            source_commits = get_commits_branch("upstream_%s" % branch_name)
            syncpoint = find_sync_point(target_commits, source_commits)

            to_pick = list()
            is_new = True
            for source_commit_hash in source_commits:
                if source_commit_hash == syncpoint:
                    is_new = False
                if is_new:
                    to_pick.append(source_commit_hash)

            while len(to_pick) > 0:
                commit_hash = to_pick.pop()
                if not git_cherry_pick(commit_hash):
                    print ("Unable to pick")
                    print ("---------------------------------------------")
                    print ('\n'.join(source_commits[commit_hash]))
        else:
            print ('unable to checkout branch')
    else:
        print ('unable sync upstream')

parser = argparse.ArgumentParser()
parser.add_argument('branch', help='branch to sync (with upstream_[BRANCH])')
inputargs = parser.parse_args()
main(branch_name=inputargs.branch)
