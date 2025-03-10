<?php
if(isset($_POST["no"]))
{
	$code = $_POST["projectId"];
	$prevPage = $_POST["previousPage"];
	header("location: ../".$prevPage."?project=".$code);
	exit();
}
else if(isset($_POST["yes"]) || isset($_POST["submit"]))
{
	session_start();
	
	$user = $_SESSION["usersName"];
	
	$code = $_POST["projectId"];
	$projectCode = $_POST["projectCode"];
	
	$action = $_POST["targetPlace"];
	$prevPage = $_POST["previousPage"];
	
	require_once 'database-handler.php';
	require_once 'functions.php';
	
	
	if($action == "Create_Project")
	{
		$title = $_POST["projectTitle"];
		$details = $_POST["projectDetails"];
		
		if(empty($title))
		{
			header("location: ../projects.php?error=emptyTitle");
			exit();
		}
		
		insertInProjects($con,$title,$details,$user);
		
		header("location: ../projects.php?error=projectCreated");
		exit();
	}
	else if($action == "Join_Project")
	{
		if(projectExists($con,$projectCode) && !isDevInProject($con,$projectCode,$user) && !isOwnerProject($con,$projectCode,$user) )
		{
			addDevToProject($con,$projectCode,$user);
			header("location: ../projects.php?error=projectJoined");
		}
		else
		{
			header("location: ../projects.php?error=projectNotFound");
		}
		exit();
	}
	else if($action== "Edit_Project")
	{
		$title = $_POST["projectName"];
		$details = $_POST["projectDetails"];
		
		if(empty($title))
		{
			header("location: ../".$prevPage."?project=".$code."&error=emptyTitle");
			exit();
		}
		
		editProject($con,$title,$details,$code);
		
		header("location: ../".$prevPage."?project=".$code."&error=projectEdited");
		exit();
	}
	else if($action == "Delete_Project")
	{
		if(existsIssueInPlace($con, "To Do", $projectCode) || existsIssueInPlace($con, "In Progress", $projectCode) || existsIssueInPlace($con, "Testing", $projectCode))
		{
			header("location: ../".$prevPage."?project=".$code."&error=existActiveIssues");
			exit();
		}
		deleteProject($con, $projectCode);
		header("location: ../projects.php");
		exit();
	}
	else if($action== "Add_Developer")
	{
		$user = $_POST["userName"];
		if(!usernameExists($con,$user))
		{
			header("location: ../".$prevPage."?project=".$code."&error=noSuchUser");
			exit();
		}
		else if(isDevInProject($con,$projectCode,$user) || $user == $_SESSION["usersName"])
		{
			header("location: ../".$prevPage."?project=".$code."&error=alreadyJoined");
			exit();
		}
		else
		{
			addDevToProject($con,$projectCode,$user);
			header("location: ../".$prevPage."?project=".$code."&error=devAdded");
		}
		exit();
	}
	else if($action== "Remove_Developer")
	{
		$target = $_POST["targetDeveloper"];
		if(empty($target))
		{
			header("location: ../".$prevPage."?project=".$code."&error=noSelection");
			exit();
		}
		removeFromProject($con, $projectCode, $target);
		
		header("location: ../".$prevPage."?project=".$code."&error=devRemoved");
		exit();
		
	}
	
	header("location: ../".$prevPage."?project=".$code);
	exit();	
}
else
{
	header("location: ../projects.php");
	exit();
}
